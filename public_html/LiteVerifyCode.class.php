<?php
// Copyright (c) 2006, Simon Jansson <http://www.litewebsite.com> all rights reserved.
// License <http://litewebsite.com/license_BSD.html>
class LiteVerifyCode{
        function __construct(){
        }// end of construct
         public static function checkSessionState(){
                // start session if not started
                if( !isset($_SESSION) ){
                        ini_set('session.use_only_cookies', 1);
                        session_start();
                }
        return true;
        }// end of checkSessionState
        public static function Code($charLength = 4){
                self::checkSessionState();
                // create random string and cut a piece of x characters (charLength)
                // string is upper case, feel free to change
                $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, $charLength));
                // register random string in session
                $_SESSION['VERIFY_CODE'] = $code;
                session_write_close();
        return true;
        }// end of code
        public static function Image($imageWidth = 90, $imageHeight = 30){
                self::checkSessionState();
					// check if code is set
                if( !isset($_SESSION['VERIFY_CODE']) ){
                        die('NO_CODE_REGISTRED_FOR_YOU');
                }
                // get code stored in session
                $code = $_SESSION['VERIFY_CODE'];
                // split code string to array
                $code = str_split($code);
                // set output headers to image
                header('Content-type: image/png');
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header("Expires: Sun, 1 Jan 2006 01:01:01 CET");
                // init image with size (width, height)
                $image = imagecreatetruecolor($imageWidth, $imageHeight);
                // background color
                imagefill($image, 0, 0, imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255)) );
                // init text position
                $textXpos = 0;
                // print graphic noise
                for($i = 0; $i < 16; $i++){
                        // random color
                        $noiseColor = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
                        // line x1, y1, x2, y2
                        $noiseX1 = rand(1, $imageWidth/2);
                        $noiseY1 = rand(1, $imageHeight);
                        $noiseX2 = rand($imageWidth/2, $imageWidth);
                        $noiseY2 = rand(1, $imageHeight);
                        // draw line
                        imageline($image, $noiseX1, $noiseY1, $noiseX2, $noiseY2, $noiseColor);
                }
                // print each character
                for($i = 0; $i < sizeof($code); $i++){
                        // font color, size and position
                        $textColor = imagecolorallocate($image, rand(30, 127), rand(30, 127), rand(30, 127));
                        $textSize = rand(3, 5); // size of built-in font
                        $textXpos += rand(12, 20); // x posistion of each char
                        $textYpos = rand(1, $imageHeight - 14); // y posistion of each char
                        // draw each character
                        imagestring($image, $textSize, $textXpos, $textYpos,  $code[$i], $textColor);
                }
                // create png image
                imagepng($image);
                imagedestroy();
        return true;
        }// end of image
}// end of class
// output image if direct access of this file
if( preg_match('/LiteVerifyCode.class.php/i', $_SERVER['SCRIPT_FILENAME']) ){
        LiteVerifyCode::Image();
        die();
}
?>
