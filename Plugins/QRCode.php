<?php
class QRCode
{
    public function __construct()
    {
        include_once 'phpqrcode/qrlib.php';
    }

    public function png($context, $outfile = false, $level = 'H', $size = 5, $margin = 1)
    {
        \QRcode::png($context, $outfile, $level, $size, $margin);
    }

    public function raw($context, $outfile = false, $level = 'H', $size = 5, $margin = 1)
    {
        return \QRcode::raw($context, $outfile, $level, $size, $margin);
    }
}