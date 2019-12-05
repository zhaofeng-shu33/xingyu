<?php
# read the configuration file
$content = file_get_contents("config.json");
$json_obj = json_decode($content);

# generate project.config.json
$input = file_get_contents("project.config.json.in");
$input = str_replace("@urlCheck@", $json_obj->urlCheck, $input);
$input = str_replace("@appid@", $json_obj->appid, $input);
file_put_contents("project.config.json", $input);

# generate config.js
$input = file_get_contents("config.js.in");
$input = str_replace("@host@", $json_obj->host, $input);
file_put_contents("config.js", $input);

?>