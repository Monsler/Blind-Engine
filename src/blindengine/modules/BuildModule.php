<?php
namespace blindengine\modules;

use std, gui, framework, blindengine;


class BuildModule extends AbstractModule
{

    /**
     * @event Build.action 
     */
    function doBuildAction(ScriptEvent $e = null)
    {    
        global $select;
        $self = fs::parent($select->file);
        mkdir($self.'/Assets');
        $dir = $self.'/Assets';
         global $CURRP; global $PROJECTS_DIR;
         $_PARENT = $PROJECTS_DIR.'/'.$CURRP;
         $_CONF = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/config.json'), 1);
         $DIR = scandir($_PARENT);
         foreach ($DIR as $v)
         {    
             $this->progressIndicator->progress .= 1;
             if($v != '.')
             {
                 if($v != '..'){
                     if(str::contains($v, '.php')){
                         $r = file_get_contents($_PARENT.'/'.$v);
                         $r = str_ireplace('$project_path', 'Assets', $r);
                         $r = $this->enc($r, '________');
                         file_put_contents($dir.'/'.$v, $r);
                     }elseif(!str::contains($v, '.php')){
                     if($v != 'config.json'){
                         if($v != 'file_conf.json'){
                             fs::copy($_PARENT.'/'.$v, $dir.'/'.$v);
                         }
                     }
                 }
                 }
             }
             
         }
         file_put_contents($dir.'/manifest.be', $this->enc('{"mainFile":"'.$_CONF['mainFile'].'"}', '_______'));
         alert('Build finished!');
         fs::copy('res://.data/ProjectBlindRuntime.exe', $select->file);
         open($self);
         $this->form(BuildForm)->hide();
         $this->form(BuildForm)->free();
    }

    
function enc($string, $key) {
    $encodedString = base64_encode($string);
    $encodedString = str_replace('=', '', $encodedString);
    $encodedString = strrev($encodedString);
    $encodedString = $key . $encodedString;
    return $encodedString;
}

// Decode
function dec($encodedString, $key) {
    $encodedString = substr($encodedString, strlen($key));
    $encodedString = strrev($encodedString);
    $encodedString = $encodedString . str_repeat('=', strlen($encodedString) % 4);
    $decodedString = base64_decode($encodedString);
    return $decodedString;
}
}
