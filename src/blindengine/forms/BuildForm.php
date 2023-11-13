<?php
namespace blindengine\forms;

use std, gui, framework, blindengine;


class BuildForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        global $CURRP;
        $this->title = 'Build '.$CURRP;
        $this->addStylesheet('/.theme/dark.css');
        global $select;
        $select = new FileChooserScript;
        $select->saveDialog = true;
        $select->initialDirectory = "C:/";
        $select->initialFileName = 'Project.exe';
        if ($select->execute())
        {
            $this->Build->call();
        }else{
            $this->free();
        }
    }

    /**
     * @event close 
     */
    function doClose(UXWindowEvent $e = null)
    {    
        $this->free();
    }
    






}
