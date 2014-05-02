<?php
/**
 *
 * imageクラス
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ image

/**
 * image Class
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */
class util_image extends xFrameworkPX_Model_Behavior
{

    // {{{ bindResize

    /**
     * リサイズメソッド
     * $destpathの画像を$srcpathにリサイズして保存します
     * 
     * @param $destpath 元の画像パス
     * @param $srcpath 保存先画像パス
     * @param $resizeW リサイズしたい幅(デフォルト320)
     * @param $resizeH リサイズしたい高さ(デフォルト240)
     * @param $bgcolor 余白を白で埋めるかどうか（デフォルトfalse）
     * @return boolean
     */
    public function bindResize(
        $destpath,
        $srcpath,
        $resizeW = 320,
        $resizeH = 240,
        $bgcolor = false
    ) {
        $filepath = '';
        $divisionH  = 1;
        $divisionW  = 1;
        $newH       = '';
        $newW       = '';
        $src         = '';
        $dst         = null;

        if ($resizeW <= 0 || $resizeH <= 0) {
            return false;
        }

        try {

            // 保存先ディレクトリ作成
            makeDirectory(dirname($srcpath));

            // 拡張子取得
            $ext = pathinfo($srcpath, PATHINFO_EXTENSION);

            // ファイルパス生成
            $filepath = $destpath;
            list($w, $h) = getimagesize($filepath);

            if ($w < $h) {

                if ($h > $resizeH) {
                    $divisionH = $h / $resizeH;
                    $newH = $h / $divisionH;
                } else {
                    $newH = $h;
                }

                $newW = $w / $divisionH;

                if ($w < $newW) {
                    $divisionW = $w / $newW;
                    $newW = $w / $divisionW;
                    $newH = $h / $divisionW;
                }

            } else if ($w > $h) {

                if ($w > $resizeW) {
                    $divisionW = $w / $resizeW;
                    $newW = $w / $divisionW;
                } else {
                    $newW = $w;
                }

                $newH = $h / $divisionW;

                if ($resizeH < $newH) {
                    $divisionH = $newH / $resizeH;
                    $newH = $newH / $divisionH;
                    $newW = $newW / $divisionH;
                }

            } else {

                if ($resizeW > $resizeH) {
                    if ($resizeW > $w) {
                        $newW = $w;
                        $newH = $h;
                    } else {
                        $newH = $resizeH;
                        $divisionH = $h / $newH;
                        $newW = $w / $divisionH;
                    }

                } else if ($resizeW < $resizeH) {
                    if ($resizeH > $h) {
                        $newW = $w;
                        $newH = $h;
                    } else {
                        $newW = $resizeW;
                        $divisionW = $w / $newW;
                        $newH = $h / $divisionW;
                    }
                } else {
                    if ($resizeW > $w) {
                        $newW = $w;
                        $newH = $h;
                    } else {
                        $newW = $resizeW;
                        $newH = $resizeH;
                    }
                }

            }

            // 拡張子判定
            if (strtolower($ext) == 'png') {

                $src = @imagecreatefrompng( $filepath );

            } else if (strtolower($ext) == 'jpg') {

                $src = imagecreatefromjpeg($filepath);

            } else if (strtolower($ext) == 'jpeg') {

                $src = @imagecreatefromjpeg($filepath);

            } else if (strtolower($ext) == 'gif') {

                $src = imagecreatefromgif($filepath);

            } else {

                return false;

            }

            $dst = imagecreatetruecolor($newW,$newH);

            imagecopyresized($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

            if ($bgcolor) {

                // 指定サイズより、縦横どちらかが小さい場合は、白にする
                if ($newW < $resizeW || $newH < $resizeH) {

                    $im = imagecreatetruecolor($resizeW, $resizeH);

                    $white = imagecolorallocatealpha($im, 255, 255, 255, 127);
                    imagefill($im, 0, 0, $white);

                    // 元画像の座標を決定
                    $x = ($resizeW - $newW) / 2;
                    $y = ($resizeH - $newH) / 2;

                    imagecopy($im, $dst, $x, $y, 0, 0, $newW, $newH);

                    // 拡張子判定
                    if (strtolower($ext) == 'png') {
                        imagepng($im, $srcpath);
                    } else if (strtolower($ext) == 'jpg') {
                        imagejpeg($im, $srcpath);
                    } else if (strtolower($ext) == 'jpeg') {
                        imagejpeg($im, $srcpath);
                    } else if (strtolower($ext) == 'gif') {
                        imagegif($im, $srcpath);
                    }
                    imagedestroy($im);
                    imagedestroy($dst);
                    imagedestroy($src);

                } else {

                    // 拡張子判定
                    if (strtolower($ext) == 'png') {
                        imagepng($dst, $srcpath);
                    } else if (strtolower($ext) == 'jpg') {
                        imagejpeg($dst, $srcpath);
                    } else if (strtolower($ext) == 'jpeg') {
                        imagejpeg($dst, $srcpath);
                    } else if (strtolower($ext) == 'gif') {
                        imagegif($dst, $srcpath);
                    }
                    imagedestroy($dst);
                    imagedestroy($src);

                }

            } else {

                // 拡張子判定
                if (strtolower($ext) == 'png') {
                    imagepng($dst, $srcpath);
                } else if (strtolower($ext) == 'jpg') {
                    imagejpeg($dst, $srcpath);
                } else if (strtolower($ext) == 'jpeg') {
                    imagejpeg($dst, $srcpath);
                } else if (strtolower($ext) == 'gif') {
                    imagegif($dst, $srcpath);
                }
                imagedestroy( $dst );
                imagedestroy( $src );

            }

        } catch (xFrameworkPX_Exception $e) {
            return false;
        }

        return true;

    }

    // }}}
    // {{{ bindGetImageInfo

    /**
     * 画像情報取得メソッド
     * 主に高さ、幅などを設定した配列を取得します。
     * 
     * @param $arg, $arg.....取得したい設定名（深い階層まで）
     * @return array
     */
    public function bindGetImageInfo()
    {

        $ret = null;
        $args = func_get_args();

        $eval = '$ret = getSetting(\'thumbnail\'';
        foreach ($args as $arg) {
            $eval .= ', \''.$arg.'\'';
        }
        $eval .= ');';

        eval($eval);

        return $ret;

    }

    // }}}

}

// }}}