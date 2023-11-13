<?php
namespace blindengine\forms;

use std, gui, framework, blindengine;

function BlindErrorHandler($errno, $errstr, $errfile, $errline)
{  global $_DEBG_F; $_DEBG_F->text .= "[ERROR] Error on line $errline in current instance:\n$errstr\n";  }

class inProject extends AbstractForm
{
    
    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->addStylesheet('/.theme/dark.css');
        global $CURRP; global $PROJECTS_DIR;
        $this->title = 'Blind Engine [Editing] '.$CURRP;
        $this->listView->items->clear();
        $d = scandir($PROJECTS_DIR.'/'.$CURRP);
        foreach ($d as $v)
        {
            if($v != '.')
            {
                if($v != '..')
                {
                    $this->listView->items->add($v);
                }
            }
        }
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        if(UXDialog::confirm('You really want to back to menu?'))
        {
            $this->form(MainForm)->show();
            $this->free();
        }
    }

    /**
     * @event listView.click-2x 
     */
    function doListViewClick2x(UXMouseEvent $e = null)
    {    
        global $_sel;
        $_sel = $this->listView->selectedItem;
        if($_sel != '')
        {
            
            global $CURRP; global $PROJECTS_DIR;
            $parse = file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json');
            //Logger::info(json_encode(json_decode($parse)));
            $parse = json_decode($parse, 1);
            
            if ($parse[$_sel] == 'def'){
                Logger::info('Opening file..');
                global $SCRIPT;
                $_READ = file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/'.$_sel);
                $this->editorField->text = $_READ;
                $SCRIPT = $this->listView->selectedItem;
                $this->teditorPanel->visible = true;
                
            }elseif($parse[$_sel] == 'sys')
            {
                open($PROJECTS_DIR.'/'.$CURRP.'/'.$_sel);
            }
        }elseif($parse[$_sel] == ''){
            Logger::error('Empty prompt');
        }
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        $this->teditorPanel->visible = false;
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
      global $SCRIPT;   global $CURRP; global $PROJECTS_DIR;
      file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/'.$SCRIPT, $this->editorField->text);
      UXDialog::show('File '.$SCRIPT.' saved.', 'INFORMATION');
    }

    /**
     * @event button4.action 
     */
    function doButton4Action(UXEvent $e = null)
    {    
        
        $this->toolsFF->visible = !$this->toolsFF->visible;
        $this->listView->enabled = !$this->listView->enabled;
            
    }

    /**
     * @event toolsFF.click-Left 
     */
    function doToolsFFClickLeft(UXMouseEvent $e = null)
    {    
        
        $_s = $this->toolsFF->selectedIndex;
        if($_s == 0)
        {
            $n = UXDialog::input('Script name:');
            if($n != '')
            {
                if($n != ' ')
                {
                    global $_sel;   global $CURRP; global $PROJECTS_DIR;
                    file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/'.$n.'.php', '<?php
//your code goes here');
                    $this->listView->items->add($n.'.php');
                    $u = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json'), 1);
                    $u[$n.'.php'] = 'def';
                    file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json', json_encode($u));
                    
                }
            }
        }elseif($_s == 1)
        {
            $expl = new FileChooserScript;
            $expl->initialDirectory = 'C:/';
            
            if($expl->execute())
            {
                $n = UXDialog::input('New file name:');
                if($n != ''){
                    if($n != ' '){
                         global $_sel;   global $CURRP; global $PROJECTS_DIR;
                        fs::copy($expl->file, $PROJECTS_DIR.'/'.$CURRP.'/'.$n);
                        $this->listView->items->clear();
                        $d = scandir($PROJECTS_DIR.'/'.$CURRP);
                        $u = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json'), 1);
                        $u[$n] = 'sys';
                        file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json', json_encode($u));
                        foreach ($d as $v)
                        {
                            if($v != '.')
                            {
                                if($v != '..')
                                {
                                    $this->listView->items->add($v);
                                }
                            }
                        }
                    }
                }
                
            }
            }elseif($_s == 2){
                $n = UXDialog::input("Visual script name:");
                if($n != ''){
                    if($n != ' ')
                    {
                        global $_sel;   global $CURRP; global $PROJECTS_DIR;
                        file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/'.$n.'.phv', 'alert("Hello, world!");');
                        $u = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json'), 1);
                        $u[$n.'.phv'] = '{"0":{"id":"alert", "param1":"Hello, world!"}}';
                         $d = scandir($PROJECTS_DIR.'/'.$CURRP);
                         $this->listView->items->clear();
                         alert('Visual script '.$n.'.phv created');
                         foreach ($d as $v)
                        {
                            if($v != '.')
                            {
                                if($v != '..')
                                {
                                    $this->listView->items->add($v);
                                }
                            }
                        }
                    }
                }
            }
        $this->toolsFF->hide();
        $this->listView->enabled = !$this->listView->enabled;
        
    }

    /**
     * @event image.click-Left 
     */
    function doImageClickLeft(UXMouseEvent $e = null)
    {    
        global $_sel;   global $CURRP; global $PROJECTS_DIR;
      
        $parser = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/config.json'), 1);
        $file = file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/'.$parser['mainFile']);
        global $_DEBG_F;
        $_DEBG_F = $this->textArea;
        $add = 'use gui, framework, std;
        function enc($string, $key) {
    $encodedString = base64_encode($string);
    $encodedString = str_replace("=", "", $encodedString);
    $encodedString = strrev($encodedString);
    $encodedString = $key . $encodedString;
    return $encodedString;
}

// Decode
function dec($encodedString, $key) {
    $encodedString = substr($encodedString, strlen($key));
    $encodedString = strrev($encodedString);
    $encodedString = $encodedString . str_repeat("=", strlen($encodedString) % 4);
    $decodedString = base64_decode($encodedString);
    return $decodedString;
}
        function script($name) {
            $f = "use framework, gui, std;\n". file_get_contents("'.$PROJECTS_DIR.'/'.$CURRP.'/$name");
            $f = str_ireplace('."'".'$project_path'."'".',"'.$PROJECTS_DIR.'/'.$CURRP.'", $f);
            eval(str_ireplace("<?php\n", "", $f));
        }
        
';




        eval($add.str_ireplace('$project_path', $PROJECTS_DIR.'/'.$CURRP, $file));


    }

    /**
     * @event imageAlt.click-Left 
     */
    function doImageAltClickLeft(UXMouseEvent $e = null)
    {
              app()->form(BuildForm)->show();
    }

    /**
     * @event button5.action 
     */
    function doButton5Action(UXEvent $e = null)
    {    
        $_sel = $this->listView->selectedItem;
        if($_sel){
            global $CURRP, $PROJECTS_DIR;
            fs::delete($PROJECTS_DIR.'/'.$CURRP.'/'.$_sel);
            $parse = json_decode(file_get_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json'), 1);
            unset($parse[$_sel]);
            $this->teditorPanel->hide();
            file_put_contents($PROJECTS_DIR.'/'.$CURRP.'/file_conf.json', json_encode($parse));
            alert($_sel.' deleted.');
            $this->listView->items->clear();
             $d = scandir($PROJECTS_DIR.'/'.$CURRP);
             foreach ($d as $v)
                        {
                            if($v != '.')
                            {
                                if($v != '..')
                                {
                                    $this->listView->items->add($v);
                                }
                            }
                        }
        }
        
    }

    /**
     * @event button6.action 
     */
    function doButton6Action(UXEvent $e = null)
    {
       $this->textArea->text = '';
    }









}
