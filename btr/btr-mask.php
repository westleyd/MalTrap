<?php
///// Trap people digging around for exploits.
//$mask = "/(install|pagead|ccbill|\.cgi|cgi\-bin|\/images|\info.php|\/join.*\.|admin|login|bug|track|redir|sup|signup|popup|pma|test|list|fag|protect|\/php|mail|engine|include|\.dll|error|xmlrpc|google|manage|soap|man|status|script|setup|\.inc|sql)/i";
//$mask = "/(install|pagead|ccbill|cgi\-bin|\/images|\/php|info.php|join|admin|login|bug|track|redir|sup|signup|popup|pma|test|list|fag|protect|mail|engine|include|error|xmlrpc|google|soap|manage|man|status|script|setup|\.cgi|\.dll|\.inc|\.asp|\.pl|sql|sign|user|regist|member|blog|module|post|reg\.php|fck|upload|file|adm|wp-|crm|msd)/i";
$mask = "/(install|pagead|ccbill|cgi\-bin|\/images|\/php|info.php|join|admin|login|bug|track|redir|sup|signup|popup|pma|test|list|fag|protect|mail|engine|include|error|xmlrpc|google|soap|manage|man|status|script|setup|\.cgi|\.dll|\.inc|\.asp|\.pl|sql|sign|user|regist|member|blog|module|post|reg\.php|fck|upload|file|adm|wp-|crm|msd|db\/|^web\/|Smileys\/SGW|avatars\/|index.php\/index.php|js\/|^start$)/i";

$goodmask = "/(favicon.ico|robots.txt)/i";
?>
