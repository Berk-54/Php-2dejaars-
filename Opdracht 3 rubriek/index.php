<?php
declare(strict_types=1);

namespace TicTacToe\Shapes {

    /**
     * Abstract parent: Figure
     */
    abstract class Figure
    {
        private string $color;

        public function __construct(string $color)
        {
            $this->setColor($color);
        }

        public function getColor(): string
        {
            return $this->color;
        }

        public function setColor(string $color): void
        {
            $allowed = ['red', 'green', 'blue', 'yellow', 'orange', 'purple'];
            $c = strtolower($color);
            if (!in_array($c, $allowed, true)) {
                throw new \InvalidArgumentException("Invalid color: $color");
            }
            $this->color = $c;
        }

        abstract public function draw(): string;
    }

    /** ---- Child classes ---- */

    class Square extends Figure
    {
        private int $length;

        public function __construct(string $color, int $length)
        {
            parent::__construct($color);
            $this->setLength($length);
        }

        public function getLength(): int { return $this->length; }
        public function setLength(int $length): void
        {
            if ($length <= 0) throw new \InvalidArgumentException('length must be > 0');
            $this->length = $length;
        }

        public function draw(): string
        {
            $l = $this->length;
            return sprintf(
                '<rect x="0" y="0" width="%d" height="%d" fill="%s" stroke="black" stroke-width="6"/>',
                $l, $l, $this->getColor()
            );
        }
    }

    class Rectangle extends Figure
    {
        private int $height;
        private int $width;

        public function __construct(string $color, int $height, int $width)
        {
            parent::__construct($color);
            $this->setHeight($height);
            $this->setWidth($width);
        }

        public function getHeight(): int { return $this->height; }
        public function getWidth(): int { return $this->width; }
        public function setHeight(int $h): void
        {
            if ($h <= 0) throw new \InvalidArgumentException('height must be > 0');
            $this->height = $h;
        }
        public function setWidth(int $w): void
        {
            if ($w <= 0) throw new \InvalidArgumentException('width must be > 0');
            $this->width = $w;
        }

        public function draw(): string
        {
            return sprintf(
                '<rect x="0" y="0" width="%d" height="%d" fill="%s" stroke="black" stroke-width="6"/>',
                $this->width, $this->height, $this->getColor()
            );
        }
    }

    class Circle extends Figure
    {
        // "length" wordt geïnterpreteerd als diameter
        private int $length;

        public function __construct(string $color, int $length)
        {
            parent::__construct($color);
            $this->setLength($length);
        }

        public function getLength(): int { return $this->length; }
        public function setLength(int $l): void
        {
            if ($l <= 0) throw new \InvalidArgumentException('length must be > 0');
            $this->length = $l;
        }

        public function draw(): string
        {
            $r  = $this->length / 2;
            $cx = $r; $cy = $r;
            return sprintf(
                '<circle cx="%s" cy="%s" r="%s" fill="%s" stroke="black" stroke-width="6"/>',
                $cx, $cy, $r, $this->getColor()
            );
        }
    }

    class Triangle extends Figure
    {
        private int $height;
        private int $width;

        public function __construct(string $color, int $height, int $width)
        {
            parent::__construct($color);
            $this->setHeight($height);
            $this->setWidth($width);
        }

        public function getHeight(): int { return $this->height; }
        public function getWidth(): int { return $this->width; }
        public function setHeight(int $h): void
        {
            if ($h <= 0) throw new \InvalidArgumentException('height must be > 0');
            $this->height = $h;
        }
        public function setWidth(int $w): void
        {
            if ($w <= 0) throw new \InvalidArgumentException('width must be > 0');
            $this->width = $w;
        }

        public function draw(): string
        {
            // Isosceles: top in het midden, basis onder
            $w = $this->width; $h = $this->height;
            $mid = (int) floor($w / 2);
            $points = sprintf('0,%d %d,0 %d,%d', $h, $mid, $w, $h);
            return sprintf(
                '<polygon points="%s" fill="%s" stroke="black" stroke-width="6"/>',
                $points, $this->getColor()
            );
        }
    }
}

namespace {
    use TicTacToe\Shapes\{Square, Rectangle, Circle, Triangle};

    // Helper om een shape te positioneren zonder draw() te wijzigen
    function at(string $svg, int $x, int $y): string {
        return sprintf('<g transform="translate(%d,%d)">%s</g>', $x, $y, $svg);
    }

    // Voorbeeld-objecten (drie kolommen x vier rijen)
    $size = 120;   // Square/diameter
    $gap  = 20;    // ruimte tussen shapes

    $rows = [
        [new Square('blue', $size),      new Square('purple', $size),      new Square('green', $size)],
        [new Circle('blue', $size),      new Circle('purple', $size),      new Circle('green', $size)],
        [new Rectangle('blue', 60, 120), new Rectangle('purple', 60, 120), new Rectangle('green', 60, 120)],
        [new Triangle('blue', 100, 120), new Triangle('purple', 100, 120), new Triangle('green', 100, 120)],
    ];

    // Canvas
    $colWidth  = $size + $gap;
    $rowHeight = $size + $gap;
    $svgWidth  = 3 * $colWidth + $gap;
    $svgHeight = 4 * $rowHeight + $gap;

    // HTML (heredoc, blijft binnen namespace-blok)
    echo <<<HTML
    <!doctype html>
    <html lang="nl">
    <head>
        <meta charset="utf-8">
        <title>OOP Opdracht 3 – Shapes (SVG)</title>
        <style>
            body { font-family: system-ui, sans-serif; padding: 24px; }
            .grid { margin-top: 12px; }
            pre { background:#f6f6f6; padding:12px; border-radius:8px; }
        </style>
    </head>
    <body>
        <h1>OOP Opdracht 3 – Shapes (SVG)</h1>
        <svg class="grid" width="{$svgWidth}" height="{$svgHeight}" viewBox="0 0 {$svgWidth} {$svgHeight}">
    HTML;

    // Teken shapes
    $y = $gap;
    foreach ($rows as $r) {
        $x = $gap;
        foreach ($r as $shape) {
            echo at($shape->draw(), $x, $y);
            $x += $colWidth;
        }
        $y += $rowHeight;
    }

    // Sluit SVG en print wat getters (eis rubric)
    echo "</svg>\n";
    echo "<h2>Voorbeeld: waardes via getters (encapsulation)</h2><pre>";

    echo "Square color: " . $rows[0][0]->getColor() . ", length: " . $rows[0][0]->getLength() . PHP_EOL;
    echo "Circle color: " . $rows[1][1]->getColor() . ", diameter: " . $rows[1][1]->getLength() . PHP_EOL;

    echo "Rectangle color: " . $rows[2][2]->getColor() . ", width x height: "
        . $rows[2][2]->getWidth() . " x " . $rows[2][2]->getHeight() . PHP_EOL;

    echo "Triangle color: " . $rows[3][0]->getColor() . ", width x height: "
        . $rows[3][0]->getWidth() . " x " . $rows[3][0]->getHeight() . PHP_EOL;

    echo "</pre></body></html>";
}
