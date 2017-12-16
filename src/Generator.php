<?php
/**
 * Created by PhpStorm.
 * User: jedy
 * Date: 12/16/17
 * Time: 2:16 PM
 */

namespace Jedy\GifGenerator;




use Jedy\GifGenerator\Exceptions\UnsupportedImageException;
use Jedy\GifGenerator\Exceptions\UnsupportedParameterException;

class Generator
{
    /**
     * @var string The gif string source
     */
    private $gif;

    /**
     * @var string Encoder version
     */
	private $version;

    /**
     * @var boolean Check the image is build or not
     */
    private $imgBuilt;

    /**
     * @var array Frames string sources
     */
	private $frameSources;

    /**
     * @var integer Gif loop
     */
	private $loop;

    /**
     * @var integer Gif dis
     */
	private $dis;

    /**
     * @var integer Gif color
     */
	private $colour;

    /**
     * @var array
     */
	private $errors;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();

        $this->version = 'GifGenerator: 1.0.0';
        $this->errors = array(
            '001-BC' => 'Please Provide Correct Parameter.',
    		'002-BC' => 'Source is not a GIF image.',
    		'003-BC' => 'You have to give resource image variables, image URL or image binary sources in $frames array.',
    		'004-BC' => 'Does not make animation from animated GIF source.',
        );
    }


    /**
     * @param array $frames
     * @param int $loop
     * @return $this
     * @throws UnsupportedImageException
     * @throws UnsupportedParameterException
     * @throws \Exception
     */
	public function create($frames = [], $loop = 0)
    {
		if (!is_array($frames)) {
            throw new UnsupportedParameterException($this->version.': '.$this->errors['001-BC']);
		}
		$this->loop = ($loop > -1) ? $loop : 0;
		$this->dis = 2;
		for ($i = 0; $i < count($frames); $i++) {

			if (is_resource($frames[$i]['image'])) {

                $resourceImg = $frames[$i]['image'];

                ob_start();
                imagegif($frames[$i]['image']);
                $this->frameSources[] = ob_get_contents();
                ob_end_clean();

            } elseif (is_string($frames[$i]['image'])) {
                if (file_exists($frames[$i]['image']) || filter_var($frames[$i]['image'], FILTER_VALIDATE_URL)) {
                    $frames[$i]['image'] = file_get_contents($frames[$i]['image']);
                }
                $resourceImg = imagecreatefromstring($frames[$i]['image']);

                ob_start();
                imagegif($resourceImg);
                $this->frameSources[] = ob_get_contents();
                ob_end_clean();

			} else { // Fail

                throw new UnsupportedParameterException($this->version.': '.$this->errors['003-BC']);
			}

            if ($i == 0) {

                $colour = imagecolortransparent($resourceImg);
            }

			if (substr($this->frameSources[$i], 0, 6) != 'GIF87a' && substr($this->frameSources[$i], 0, 6) != 'GIF89a') {

                throw new UnsupportedImageException($this->version.': '.$i.' '.$this->errors['002-BC']);
			}

			for ($j = (13 + 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07))), $k = TRUE; $k; $j++) {
				switch ($this->frameSources[$i] { $j }) {
					case '!':
						if ((substr($this->frameSources[$i], ($j + 3), 8)) == 'NETSCAPE') {
                            throw new \Exception($this->version.': '.$this->errors['004-BC'].' ('.($i + 1).' source).');
						}
					break;
					case ';':
						$k = false;
					break;
				}
			}
            unset($resourceImg);
		}
        if (isset($colour)) {

            $this->colour = $colour;

        } else {

            $red = $green = $blue = 0;
            $this->colour = ($red > -1 && $green > -1 && $blue > -1) ? ($red | ($green << 8) | ($blue << 16)) : -1;
        }

		$this->addHeader();

		for ($i = 0; $i < count($this->frameSources); $i++) {

			$this->addFrames($i, $frames[$i]['millisecond']);
		}

		$this->addFooter();

        return $this;
	}

    protected function addHeader()
    {
		$cmap = 0;

		if (ord($this->frameSources[0] { 10 }) & 0x80) {

			$cmap = 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07));

			$this->gif .= substr($this->frameSources[0], 6, 7);
			$this->gif .= substr($this->frameSources[0], 13, $cmap);
			$this->gif .= "!\377\13NETSCAPE2.0\3\1".$this->encodeAsciiToChar($this->loop)."\0";
		}
	}

    /**
     * @param $i
     * @param $d
     */
    protected function addFrames($i, $d)
    {
		$Locals_str = 13 + 3 * (2 << (ord($this->frameSources[ $i ] { 10 }) & 0x07));

		$Locals_end = strlen($this->frameSources[$i]) - $Locals_str - 1;
		$Locals_tmp = substr($this->frameSources[$i], $Locals_str, $Locals_end);

		$Global_len = 2 << (ord($this->frameSources[0 ] { 10 }) & 0x07);
		$Locals_len = 2 << (ord($this->frameSources[$i] { 10 }) & 0x07);

		$Global_rgb = substr($this->frameSources[0], 13, 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07)));
		$Locals_rgb = substr($this->frameSources[$i], 13, 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07)));

		$Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 0).chr(($d >> 0 ) & 0xFF).chr(($d >> 8) & 0xFF)."\x0\x0";

		if ($this->colour > -1 && ord($this->frameSources[$i] { 10 }) & 0x80) {

			for ($j = 0; $j < (2 << (ord($this->frameSources[$i] { 10 } ) & 0x07)); $j++) {

				if (ord($Locals_rgb { 3 * $j + 0 }) == (($this->colour >> 16) & 0xFF) &&
					ord($Locals_rgb { 3 * $j + 1 }) == (($this->colour >> 8) & 0xFF) &&
					ord($Locals_rgb { 3 * $j + 2 }) == (($this->colour >> 0) & 0xFF)
				) {
					$Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 1).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF).chr($j)."\x0";
					break;
				}
			}
		}

		switch ($Locals_tmp { 0 }) {

			case '!':

				$Locals_img = substr($Locals_tmp, 8, 10);
				$Locals_tmp = substr($Locals_tmp, 18, strlen($Locals_tmp) - 18);

			break;

			case ',':

				$Locals_img = substr($Locals_tmp, 0, 10);
				$Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);

			break;
		}

		if (ord($this->frameSources[$i] { 10 }) & 0x80 && $this->imgBuilt) {

			if ($Global_len == $Locals_len) {

				if ($this->blockCompare($Global_rgb, $Locals_rgb, $Global_len)) {

					$this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;

				} else {

					$byte = ord($Locals_img { 9 });
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |= (ord($this->frameSources[0] { 10 }) & 0x07);
					$Locals_img { 9 } = chr($byte);
					$this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
				}

			} else {

				$byte = ord($Locals_img { 9 });
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |= (ord($this->frameSources[$i] { 10 }) & 0x07);
				$Locals_img { 9 } = chr($byte);
				$this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
			}

		} else {

			$this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;
		}

		$this->imgBuilt = true;
	}

    protected function addFooter()
    {
		$this->gif .= ';';
	}

    /**
     * @param $globalBlock
     * @param $localBlock
     * @param $length
     * @return int
     */
	protected function blockCompare($globalBlock, $localBlock, $length)
    {
		for ($i = 0; $i < $length; $i++) {

			if ($globalBlock { 3 * $i + 0 } != $localBlock { 3 * $i + 0 } ||
				$globalBlock { 3 * $i + 1 } != $localBlock { 3 * $i + 1 } ||
				$globalBlock { 3 * $i + 2 } != $localBlock { 3 * $i + 2 }) {

                return 0;
			}
		}

		return 1;
	}

    /**
     * @param $char
     * @return string
     */
    protected function encodeAsciiToChar($char)
    {
		return (chr($char & 0xFF).chr(($char >> 8) & 0xFF));
	}


    protected function reset()
    {
        $this->frameSources;
        $this->gif = 'GIF89a';
        $this->imgBuilt = false;
        $this->loop = 0;
        $this->dis = 2;
        $this->colour = -1;
    }

    /**
     * @param bool $binary
     * @return string
     */
	public function show($binary = false)
    {
        if ($binary){
            header('Content-type: image/gif');
            header('Content-Disposition: filename="butterfly.gif"');
            echo $this->gif;
            exit;
        }
        return $this->gif;
	}

    /**
     * @param string $path
     * @param $filename
     */
    public function store($path = '',$filename)
    {
        file_put_contents($path.'/'.$filename, $this->gif);
	}
}