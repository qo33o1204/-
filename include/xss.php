<?php

require_once('../htmlpurifier/library/HTMLPurifier.auto.php');

function xsspurify($InString) {
    $config = HTMLPurifier_Config::createDefault();
    // configuration goes here:
    $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
    $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
    $config->set('Cache.SerializerPath', '/tmp');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($InString);
//   return htmlspecialchars($InString);
}
// function xss($InString) {
    // return htmlspecialchars($InString);
// }
?>
