<?php
namespace blindengine\forms;

use bundle\zip\ZipFileScript;
use php\compress\ZipFile;
use std, gui, framework, blindengine;


class MainForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->addStylesheet('/.theme/dark.css');
        if (!file_exists('.beprojects')){
            mkdir('.beprojects');
            Logger::info('Created projects folder.');
        }
        global $PROJECTS_DIR;
        $PROJECTS_DIR = '.beprojects';
        $d = scandir($PROJECTS_DIR);
        foreach ($d as $v)
        {
            if ($v != '.')
            {
                if ($v != '..')
                {
                    $this->listView->items->add($v);
                }
            }
        }
        //$this->image3->image = new UXImage('res://.data/img/build.png');
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        global $PROJECTS_DIR;
        $n = UXDialog::input('Project name:');
        if ($n != ' ')
        {
            if ($n != '')
            {
                $this->listView->items->add($n);
                mkdir($PROJECTS_DIR.'/'.$n);
                fs::copy('res://.data/img/ico1.png', $PROJECTS_DIR.'/'.$n.'/logo.png');
                file_put_contents($PROJECTS_DIR.'/'.$n.'/config.json', '{"defaultTitle": "'.$n.'", "BlindVersion": "BETA", "versionString": "1.0", "mainFile": "main.php"}');
                file_put_contents($PROJECTS_DIR.'/'.$n.'/main.php', '// Simple code, that creates new window.
use std, framework, gui;
global $WIN; $WIN = new UXForm(); $WIN->title = "'.$n.'";
$WIN->size = [700, 600]; // Set size for window
$WIN->show(); // Show the window
$WIN->icons->add("$project_path/logo.png"); // Set form icon
// Click run button at the top bar to run this project.');
                file_put_contents($PROJECTS_DIR.'/'.$n.'/file_conf.json', '{"main.php": "def", "config.json": "def"}');
                UXDialog::show('Project successfully created.', 'INFORMATION');
                Logger::info('Create instance '.$n.'.');
                
            }else{
                UXDialog::show('Invalid project name.', 'ERROR');
            }
        }else{
                UXDialog::show('Invalid project name.', 'ERROR');
        }
        
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $SP = $this->listView->selectedItem;
        global $PROJECTS_DIR;
        $d = scandir($PROJECTS_DIR.'/'.$SP);
        if($SP != ''){
        if(UXDialog::confirm('Do you really want to remove selected project? IT WILL DELETE ALL THE FILES!'))
        {
            foreach ($d as $v) {
                 if ($v != '.')
                 {
                     if($v != '..')
                     {
                         fs::delete($PROJECTS_DIR.'/'.$SP.'/'.$v);
                         Logger::debug('Delete '.$PROJECTS_DIR.'/'.$SP.'/'.$v);
                     }
                 }   
            }
            fs::delete($PROJECTS_DIR.'/'.$SP); 
            UXDialog::show('Project '.$SP.' successfully deleted.', 'INFORMATION');
            $this->listView->items->remove($SP);
        }
        }
    }

    /**
     * @event listView.click-2x 
     */
    function doListViewClick2x(UXMouseEvent $e = null)
    {    
        if($this->listView->selectedItem)
        {
            $this->hide();
            global $CURRP;
            $CURRP = $this->listView->selectedItem;
            app()->form(inProject)->show();
            $this->free();
            
        }
    }

    /**
     * @event imageAlt.click-Left 
     */
    function doImageAltClickLeft(UXMouseEvent $e = null)
    {    
        browse('https://www.youtube.com/channel/UCXeLBBv6CHoXepmPmCvmnCQ');
    }

    /**
     * @event image.click-Left 
     */
    function doImageClickLeft(UXMouseEvent $e = null)
    {    
        browse('https://discord.gg/6gqJcPKRJ6');
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        $this->free();
    }

    /**
     * @event button4.action 
     */
    function doButton4Action(UXEvent $e = null)
    {    
        global $PROJECTS_DIR;
        $_s = $this->listView->selectedItem;
        if($_s)
        {
            $zp = new ZipFileScript;
            $zp->autoCreate = true;
            $ap = new FileChooserScript;
            $ap->initialDirectory = 'C:/';
            $ap->initialFileName = 'project.beproject';
            $ap->saveDialog = true;
            $ap->filterExtensions = '*.beproject*';
            if($ap->execute()){
                $zp->path = fs::parent($ap->file).'/'.$_s.'.beproject';
                $zp->addDirectory(fs::abs($PROJECTS_DIR.'/'.$_s));
                alert('Project saved on device: '.fs::parent($ap->file));
            }
        }
        
    }

    /**
     * @event button5.action 
     */
    function doButton5Action(UXEvent $e = null)
    {    
        global $PROJECTS_DIR;
        $ap = new FileChooserScript;
        $ap->initialDirectory = 'C:/';
        $ap->filterExtensions = "*.beproject*";
        if($ap->execute())
        {
            $z = new ZipFileScript;
            $z->path = fs::abs($ap->file);
            $z->unpack($PROJECTS_DIR.'/'.fs::nameNoExt($ap->file));
            $this->listView->items->clear();
                $d = scandir($PROJECTS_DIR);
                foreach ($d as $v)
                {
                if ($v != '.')
                {
                    if ($v != '..')
                    {
                        $this->listView->items->add($v);
                    }
                }
                
            }
            alert('Imported.');
        }
    }

    /**
     * @event button6.action 
     */
    function doButton6Action(UXEvent $e = null)
    {    
        global $PROJECTS_DIR;
        $sel = $this->listView->selectedItem;
        $ask = UXDialog::input("New name:");
        if($ask != ''){
            if($ask != ' '){
                if($sel != ''){
                Logger::info('Rename');
                rename($PROJECTS_DIR.'/'.$sel, $PROJECTS_DIR.'/'.$ask);
                    $this->listView->items->clear();
                $d = scandir($PROJECTS_DIR);
                foreach ($d as $v)
                {
                if ($v != '.')
                {
                    if ($v != '..')
                    {
                        $this->listView->items->add($v);
                    }
                }
                }
                
            }
        }
        }
        
                
    }

    /**
     * @event image4.click-Left 
     */
    function doImage4ClickLeft(UXMouseEvent $e = null)
    {    
        global $PROJECTS_DIR;
        $this->listView->items->clear();
        $d = scandir($PROJECTS_DIR);
        foreach ($d as $v)
        {
            if ($v != '.')
            {
                if ($v != '..')
                {
                    $this->listView->items->add($v);
                }
            }
        }
    }




}
