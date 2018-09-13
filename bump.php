<?php

chdir(__DIR__);

const SOURCE_FILE = __DIR__ . '/src/Travelhood/OtpSimplePay/Service.php';

$LEVEL_NAMES = ['major', 'minor', 'patch'];
$LEVEL_KEYS = array_flip($LEVEL_NAMES);

function parseSemantic($in)
{
    if(!preg_match('/([\d]+)\.([\d]+)\.([\d]+)/', $in, $matches)) {
        throw new RuntimeException('Invalid version format: '.$in);
    }
    return [
        intval($matches[1]),
        intval($matches[2]),
        intval($matches[3]),
    ];
}

function parseLevel($params)
{
    if(count($params)>0) {
        switch($params[0]) {
            case 'patch':
                return 2;
            case 'minor':
                return 1;
            case 'major':
                return 0;
        }
        throw new InvalidArgumentException('Invalid parameter: '.$params[0]);
    }
    return 2;
}

function getLastTag()
{
    $lastTag = [0,0,0];
    $tags = array_map(function($i) { return trim($i); }, explode("\n", trim(`git tag`)));
    foreach($tags as $tag) {
        $tag = parseSemantic($tag);
        if($lastTag[0] == $tag[0]) {
            if($lastTag[1] == $tag[1]) {
                if($lastTag[2] < $tag[2]) {
                    $lastTag[2] = $tag[2];
                }
            }
            elseif($lastTag[1] < $tag[1]) {
                $lastTag[1] = $tag[1];
                $lastTag[2] = $tag[2];
            }
        }
        elseif($lastTag[0] < $tag[0]) {
            $lastTag[0] = $tag[0];
            $lastTag[1] = $tag[1];
            $lastTag[2] = $tag[2];
        }
    }
    return $lastTag;
}

function bumpTag($tag, $level)
{
    if($level == 0) {
        $tag[0]++;
        $tag[1] = 0;
        $tag[2] = 0;
    }
    elseif($level == 1) {
        $tag[1]++;
        $tag[2] = 0;
    }
    elseif($level == 2) {
        $tag[2]++;
    }
    return $tag;
}

function replaceInSource($filePath, $newVersion)
{
    $source = file_get_contents($filePath);
    $new = preg_replace('/const VERSION \= \'([^\']+)\'\;/', 'const VERSION = \'travelhood-'.$newVersion.'\';', $source, 1);
    return file_put_contents($filePath, $new);
}

$params = $argv;
array_shift($params);

$level = parseLevel($params);
echo 'Bumping ', $LEVEL_NAMES[$level], PHP_EOL;

$lastTag = getLastTag();
$newTag = bumpTag($lastTag, $level);
$newVersion = 'v'.join('.', $newTag);
echo 'New tag will be ', $newVersion, PHP_EOL;

if(!replaceInSource(SOURCE_FILE, $newVersion)) {
    throw new RuntimeException('Failed to replace version in source file: '.SOURCE_FILE);
}

echo `git commit . -m "bump to ${newVersion}"`;
echo `git tag ${newVersion}`;
echo "Don't forget to push to remote!", PHP_EOL;
