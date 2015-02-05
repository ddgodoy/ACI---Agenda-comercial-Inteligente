<?php

namespace ACI\FrontendBundle\Services;

use Sabberworm\CSS\Parser;

/**
 * Description of CmsService
 *
 * @author rachid
 */
class CmsService {

    private $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Devuelve el tema establecido
     */
    public function getTheme() {
        return "Tema";
    }

    /**
     * Devuelve una pagina en el modo edicion o publicacion
     * @mode: edit/publish
     *
     */
    public function getPage($mode = 'edit') {
        return "Tema";
    }

    public function RandomString($length = 10, $uc = TRUE, $n = TRUE, $sc = FALSE) {
        $source = 'abcdefghijklmnopqrstuvwxyz';
        if ($uc == 1)
            $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($n == 1)
            $source .= '1234567890';
        if ($sc == 1)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $source = str_split($source, 1);
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($source));
                $rstr .= $source[$num - 1];
            }
        }
        return $rstr;
    }

    public function scan_directory_recursively($directory, $filter = FALSE) {
// if the path has a slash at the end we remove it here
        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }

// if the path is not valid or is not a directory ...
        if (!file_exists($directory) || !is_dir($directory)) {
// ... we return false and exit the function
            return FALSE;

// ... else if the path is readable
        } elseif (is_readable($directory)) {
// initialize directory tree variable
            $directory_tree = array();

// we open the directory
            $directory_list = opendir($directory);

// and scan through the items inside
            while (FALSE !== ($file = readdir($directory_list))) {
// if the filepointer is not the current directory
// or the parent directory
                if ($file != '.' && $file != '..') {
// we build the new path to scan
                    $path = $directory . '/' . $file;

// if the path is readable
                    if (is_readable($path)) {
// we split the new path by directories
                        $subdirectories = explode('/', $path);

// if the new path is a directory
                        if (is_dir($path)) {
// add the directory details to the file list
                            $directory_tree[] = array(
                                'path' => $path,
                                'name' => end($subdirectories),
                                'kind' => 'directory',
                                // we scan the new path by calling this function
                                'content' => $this->scan_directory_recursively($path, $filter));

// if the new path is a file
                        } elseif (is_file($path)) {
// get the file extension by taking everything after the last dot
                            $extension = end(explode('.', end($subdirectories)));

// if there is no filter set or the filter is set and matches
                            if ($filter === FALSE || $filter == $extension) {
// add the file details to the file list
                                $directory_tree[] = array(
                                    'path' => $path,
                                    'name' => end($subdirectories),
                                    'extension' => $extension,
                                    'size' => filesize($path),
                                    'kind' => 'file');
                            }
                        }
                    }
                }
            }
// close the directory
            closedir($directory_list);

// return file list
            return $directory_tree;

// if the path is not readable ...
        } else {
// ... we return false
            return FALSE;
        }
    }

    public function getFileList($dir) { // array to hold return value
        $retval = array();
        if (substr($dir, -1) != "/")
            $dir .= "/";
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) { // skip hidden files
            if ($entry[0] == ".")
                continue;
            if (is_dir("$dir$entry")) {
                $retval[] = array("name" => "$dir$entry/", "type" => filetype("$dir$entry"), "size" => 0, "lastmod" => filemtime("$dir$entry"));
            } elseif (is_readable("$dir$entry")) {
                $retval[] = array("name" => "$dir$entry", "type" => mime_content_type("$dir$entry"), "size" => filesize("$dir$entry"), "lastmod" => filemtime("$dir$entry"));
            }
        } $d->close();
        return $retval;
    }

    public function parseXMl() {
        $directories = glob("vendor/themes/*/bootstrap.min.css");

        $my_file = 'vendor/themes/themes.txt';
        unlink($my_file);
        $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
        $i = 0;
        $len = count($directories);
        foreach ($directories as $directory) {
            $dir = array();
            $dir = explode("/", $directory);
            $url = "http://www.colourlovers.com/api/palette/" . $dir[2];
            $xml = simplexml_load_file($url);
            $color0 = $xml->palette->colors->hex[0];
            $color1 = $xml->palette->colors->hex[1];
            $color2 = $xml->palette->colors->hex[2];
            $color3 = $xml->palette->colors->hex[3];
            $color4 = $xml->palette->colors->hex[4];
            if ($i == $len - 1)
                fwrite($handle, $dir[2] . "-" . $color0 . "-" . $color1 . "-" . $color2 . "-" . $color3 . "-" . $color4);
            else
                fwrite($handle, $dir[2] . "-" . $color0 . "-" . $color1 . "-" . $color2 . "-" . $color3 . "-" . $color4 . "/");

            $i++;
        }
        fclose($handle);
        echo "Se han parseado correctamente " . count($directories) . " temas.";
    }

    public function parseAllThemes() {
        $data = null;
        $files = array();
        $colores = null;
        $my_file = 'vendor/themes/themes.txt';
        unlink($my_file);
        foreach (glob("vendor/themes/*/*.css") as $file_stream) {
            $files[] = $file_stream;
        }

        $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
        foreach ($files as $file) {
            $oCssParser = new Parser(file_get_contents($file, NULL, NULL, 1000, 100000));
            $oCss = $oCssParser->parse();
            $colores = array();
            foreach ($oCss->getAllDeclarationBlocks() as $oBlock) {
                foreach ($oBlock->getSelectors() as $oSelector) {
                    if ($oSelector->getSelector() == "body") {
                        $oRule = $oBlock->getRules('background-color');
                        if ($oRule) {
                            $oRule = $oRule[0];
                            $colores[] = $oRule->getValue();
                        }
                    } else if ($oSelector->getSelector() == ".jumbotron") {
                        $oRule2 = $oBlock->getRules('background-color');
                        if ($oRule2) {
                            $oRule2 = $oRule2[0];
                            $colores[] = $oRule2->getValue();
                        }
                    } else if ($oSelector->getSelector() == ".btn-primary") {
                        $oRule3 = $oBlock->getRules('background-color');
                        if ($oRule3) {
                            $oRule3 = $oRule3[0];
                            $colores[] = $oRule3->getValue();
                        }
                    } else if ($oSelector->getSelector() == ".navbar-default") {
                        $oRule4 = $oBlock->getRules('background-color');
                        if ($oRule4) {
                            $oRule4 = $oRule4[0];
                            $colores[] = $oRule4->getValue();
                        }
                    } else if ($oSelector->getSelector() == "small") {
                        $oRule5 = $oBlock->getRules('color');
                        if ($oRule5) {
                            $oRule5 = $oRule5[0];
                            $colores[] = $oRule5->getValue();
                        }
                    }
                }
            }
            $file_explode = explode("/", $file);
            $data = null;
            $data = $file_explode[2] . "-";
            $i = 0;
            $len = count($colores);
            foreach ($colores as $color) {
                if ($i == $len - 1)
                    $data .=$color;
                else
                    $data .=$color . "-";
                $i++;
            }
            $data.="/";
            fwrite($handle, $data);
        }

        fclose($handle);
    }

    public function read_file($filename = '') {
        $array = explode("/", file_get_contents($filename));
        return $array;
    }

}

?>
