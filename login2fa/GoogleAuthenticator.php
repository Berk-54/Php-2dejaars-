<?php
declare(strict_types=1);

/**
 * Simple Google Authenticator (TOTP) implementation.
 * - createSecret()
 * - getQRCodeGoogleUrl()
 * - verifyCode()
 *
 * Works without namespaces/composer. Compatible with Google Authenticator.
 */
final class GoogleAuthenticator
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function createSecret(int $length = 16): string
    {
        // 16 base32 chars = 80 bits secret (prima voor TOTP)
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, 31)];
        }
        return $secret;
    }

    public function getQRCodeGoogleUrl(string $issuer, string $secret, string $accountName = ''): string
    {
        // otpauth URI (standaard voor authenticator apps)
        $label = $accountName !== '' ? $accountName : 'user';
        $otpauth = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($issuer),
            rawurlencode($label),
            rawurlencode($secret),
            rawurlencode($issuer)
        );

        // QR via Google Chart API (simpel en werkt meestal nog prima)
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . rawurlencode($otpauth);
    }

    public function verifyCode(string $secret, string $code, int $discrepancy = 1, int $timeSlice = null): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if ($code === '' || !ctype_digit($code)) {
            return false;
        }

        if ($timeSlice === null) {
            $timeSlice = (int) floor(time() / 30);
        }

        // controleer huidige timeslice ± discrepancy
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calc = $this->getCode($secret, $timeSlice + $i);
            if (hash_equals($calc, str_pad($code, 6, '0', STR_PAD_LEFT))) {
                return true;
            }
        }
        return false;
    }

    private function getCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);

        // 8-byte time counter (big endian)
        $time = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation
        $offset = ord(substr($hash, -1)) & 0x0F;
        $part = substr($hash, $offset, 4);

        $value = unpack('N', $part)[1] & 0x7FFFFFFF;
        $mod = $value % 1000000;

        return str_pad((string)$mod, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);

        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        for ($i = 0, $len = strlen($b32); $i < $len; $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) continue;

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}
