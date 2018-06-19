<?php

chdir(__DIR__);

const SOURCE_FILE = __DIR__ . '/src/Travelhood/OtpSimplePay/Service.php';

function parseSemantic($in)
{
    if(!preg_match('/([\d]+)\.([\d]+)\.([\d]+)/', $in, $matches)) {
        throw new RuntimeException('Invalid version format: '.$in);
    }
    return [
        'major' => intval($matches[1]),
        'minor' => intval($matches[2]),
        'patch' => intval($matches[3]),
    ];
}

function parseLevel($params)
{
    $level = 'patch';
    if(count($params)>0) {
        switch($params[0]) {
            case 'patch':
            case 'minor':
            case 'major':
                $level = $params[0];
                break;
            default:
                throw new InvalidArgumentException('Invalid parameter: '.$params[0]);
                break;
        }
    }
    return $level;
}

function getLastTag()
{
    $tags = array_filter(explode("\n", `git tag`));
    return parseSemantic($tags[count($tags)-1]);
}

function replaceInSource($filePath, $newVersion)
{
    $source = file_get_contents($filePath);
    $new = preg_replace('/const VERSION \= \'([\']+)\'\;/', 'const VERSION = \'travelhood-'.$newVersion.'\';', $source, 1);
    return file_put_contents($filePath, $new);
}

$params = $argv;
array_shift($params);

$level = parseLevel($params);
$lastTag = getLastTag();
$newTag = $lastTag;
$newTag[$level]++;
$newVersion = 'v'.join('.', $newTag);
if(!replaceInSource(SOURCE_FILE, $newVersion)) {
    throw new RuntimeException('Failed to replace version in source file: '.SOURCE_FILE);
}

echo `git tag ${newVersion}`;
echo `git commit . -m "bump to ${newVersion}"`;
echo "Don't forget to push to remote!", PHP_EOL;
