<?php

namespace GoBonus\AppBundle\Services;

use Symfony\Component\DomCrawler\Crawler;

/**
 *
 */
class CrawlerService {

    public function parseHtml($url, $publication) {
        $pos = strpos($url, "http");
        if ($pos === false) {
            $url = "http://" . $url;
        }
        $check = false;
        $checkdata = false;
        if ($stream = @file_get_contents($url)) {
            try {
                $crawler = new Crawler($stream);

                $text_publication = html_entity_decode(strip_tags($publication->getText()));
                /*$text_publication = preg_replace('/\s+/', '', $text_publication);
                $text_publication = str_replace('&ldquo;', '', $text_publication);
                $text_publication = str_replace('&rdquo;', '', $text_publication);
                $text_publication = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $text_publication);*/
                $publicationname = preg_replace('/\s+/', '', $publication->getName());
                $publicationname2 = htmlentities(preg_replace('/\s+/', '', $publication->getName()));

                $nodeValues = $crawler->filter('a');
                foreach ($nodeValues as $node) {
                    $prueba1 = $node->textContent;
                    $prueba1 = preg_replace('/\s+/', '', $prueba1);
                    $prueba2 = htmlentities($node->textContent);
                    $prueba2 = preg_replace('/\s+/', '', $prueba2);
                    if (stripos($prueba1, $publicationname) !== false || stripos($prueba2, $publicationname2) !== false) {
                        $check = true;
                        break;
                    }
                }

                $link = $crawler->filter('.pagAnuCuerpoAnu');


                $prueba3 = $link->first()->getNode(0)->textContent;
                
                /*$prueba3 = preg_replace('/\s+/', '', $prueba3);
                $prueba3 = str_replace('&ldquo;', '', $prueba3);
                $prueba3 = str_replace('&rdquo;', '', $prueba3);
                $prueba3 = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $prueba3);
                $prueba3 = strip_tags($prueba3);*/
                $prueba3 = $this->sanear_string($prueba3);
                $text_publication = $this->sanear_string($text_publication);

                if (stripos($text_publication, $prueba3) !== false)
                    $checkdata = true;
            } catch (Exception $exc) {
                return false;
            }
        } else {
            return false;
        }

        //if ($check && $checkdata)
        if ($check)
            return true;
        else
            return false;
    }

    public function testHtml($url, $entity_publications) {
        $pos = strpos($url, "http");
        if ($pos === false) {
            $url = "http://" . $url;
        }


        $text_publication = strip_tags($entity_publications->getText());
        $text_publication = preg_replace('/\s+/', '', $text_publication);
        $text_publication = str_replace('&ldquo;', '', $text_publication);
        $text_publication = str_replace('&rdquo;', '', $text_publication);
        $text_publication = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $text_publication);
        $publicationname = preg_replace('/\s+/', '', $entity_publications->getName());
        $publicationname2 = htmlentities(preg_replace('/\s+/', '', $entity_publications->getName()));

        if ($stream = @file_get_contents($url)) {
            $crawler = new Crawler($stream);


//            $nodeValues = $crawler->filter('a');
//            foreach ($nodeValues as $node) {
//                $prueba1 = $node->textContent;
//                $prueba1 = preg_replace('/\s+/', '', $prueba1);
//                $prueba2 = htmlentities($node->textContent);
//                $prueba2 = preg_replace('/\s+/', '', $prueba2);
//
//                echo $prueba1;
//                echo $prueba2;
//                if (stripos($prueba1, $publicationname) !== false || stripos($prueba2, $publicationname2) !== false) {
//                    $check = true;
//                    echo "diole";
//                    break;
//                }
//            }

            $link = $crawler->filter('.pagAnuCuerpoAnu');
            $prueba = htmlentities(preg_replace('/\s+Â/', '', $link->first()->getNode(0)->textContent));
            $prueba3 = htmlentities($link->first()->getNode(0)->textContent);
            $prueba3 = preg_replace('/\s+/', '', $prueba3);
            $prueba3 = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $prueba3);
            echo $link->first()->getNode(0)->textContent;
            echo "<br>";
            echo $prueba;
            echo "<br>";
            echo $prueba3;
            echo "<br>";


            echo $text_publication;

            if (strpos($text_publication, $prueba) !== false || strpos($text_publication, $prueba3) !== false)
                echo "validado";
            else
                echo "no";
        }
    }

    /**
    * Reemplaza todos los acentos por sus equivalentes sin ellos
    *
    * @param $string
    *  string la cadena a sanear
    *
    * @return $string
    *  string saneada
    */
   public function sanear_string($string)
   {

       $string = trim($string);

       $string = str_replace(
           array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
           array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
           $string
       );

       $string = str_replace(
           array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
           array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
           $string
       );

       $string = str_replace(
           array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
           array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
           $string
       );

       $string = str_replace(
           array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
           array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
           $string
       );

       $string = str_replace(
           array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
           array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
           $string
       );

       $string = str_replace(
           array('ñ', 'Ñ', 'ç', 'Ç'),
           array('n', 'N', 'c', 'C',),
           $string
       );

       //Esta parte se encarga de eliminar cualquier caracter extraño
       $string = str_replace(
           array("\\", "¨", "º", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":", ".", "-",
                " "),
           '',
           $string
       );


       return $string;
   }
}
