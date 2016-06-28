<?php
require_once ('lib/savant/Savant3.php');
/**
 * A simple wrapper around the Savant3 template class.
 *
 * To be used as the base template class for all Plans templating.
 */
class Plans_Savant3 extends Savant3 {
    public function __construct() {
        parent::__construct();
        $this->tag_attributes = '';
    }
}
/**
 * A base class to be extended by interface classes.
 *
 * Sets up some default behavior that may be overridden as desired.
 * Children depend on this setting certain properties on the templates,
 * so be cautious with changing this (unless it is to simply set a
 * generic template for an object that previously had no template set).
 */
abstract class BaseInterface implements DisplayInterface {

    protected $element_ids = array();

    public function display_page(PlansPage $page) {
        $tpl = $this->setup_page($page);
        $tpl->display();
    }
    public function setup_page(PlansPage $page) {
        $tpl = new Plans_Savant3();
        $tpl->page_title = $page->title;
        $tpl->stylesheets = $page->stylesheets;
        $tpl->scripts = $this->get_local_jsfiles($page);
        $tpl->body_id = 'planspage_' . strtolower($page->identifier);
        $tpl->body_class = strtolower($page->group);
        $tpl->mainpanel_template = $this->setup_mainpanel($page);
        $tpl->contents = array_map(array($this, 'setup_widget'), $page->contents);
        $tpl->footer_template = $this->setup_footer($page->footer);
        return $tpl;
    }
    protected function setup_mainpanel(PlansPage $page) {
        $panel = $page->mainpanel;
        $tpl = new Plans_Savant3();
        $tpl->linkhome_template = $this->setup_linkhome($panel->linkhome);
        $tpl->fingerbox_template = $this->setup_fingerbox($panel->fingerbox);
        $tpl->links_template = $this->setup_links($panel->links);
        $tpl->autoread_template = $this->setup_autoreads($panel->autoreads, $page->autoreadpriority);
        return $tpl;
    }
    protected function setup_linkhome(Hyperlink $link) {
        $tpl = new Plans_Savant3();
        $tpl->href = htmlentities(html_entity_decode($link->href));
        $tpl->description = 'Home';
        return $tpl;
    }
    protected function setup_fingerbox(Form $finger) {
        $tpl = $this->setup_widget($finger);
        return $tpl;
    }
    protected function setup_links(WidgetList $links) {
        $tpl = $this->setup_widget($links);
        return $tpl;
    }
    /**
     * @param WidgetList|null $autoreads
     * @param int $lvl
     */
    protected function setup_autoreads(WidgetList $autoreads, $lvl) {
        if (!$autoreads) {
            return false;
        }
        $tpl = $this->setup_widget($autoreads);
        foreach($autoreads->contents as $i => $ar) {
            $t = $tpl->contents[$i];
            if ($ar->priority == $lvl) {
                $t->list_attributes.= ' current';
            } else {
                $t->list_attributes.= ' notcurrent';
            }
        }
        return $tpl;
    }
    protected function get_local_jsfiles($page) {
        $jsfile_arr = array();
        $jsfile_arr[] = '//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
        $jsfile_arr[] = 'js/common.js';
        // Populate the array with any files we need
        switch ($page->identifier) {
            case 'edit':
                $jsfile_arr[] = 'js/edit.js';
            break;
        }
        return $jsfile_arr;
    }
    protected function setup_footer($footer) {
        $tpl = new Plans_Savant3();
        if ($footer->doyouread) {
            $tpl->doyouread_link_template = $this->setup_widget($footer->doyouread);
        } else {
            $tpl->doyouread_link_template = null;
        }
        $tpl->legal_template = $this->setup_widget($footer->legal);
        $tpl->powered_by = $this->setup_widget($footer->powered_by);
        return $tpl;
    }
    protected static function id_and_class($id, $class) {
        $out = array();
        if ($id != null) {
            $out[] = 'id="' . $id . '"';
        }
        if (!is_array($class)) {
            $class = array($class);
        }
        $class = array_filter($class);
        if ($class) {
            $out[] = 'class="' . implode(' ', $class) . '"';
        }
        return ' ' . implode(' ', $out);
    }
    protected function setup_widget(Widget $obj) {
        $tpl = new Plans_Savant3();
        if ($obj instanceof NotesTopic && $obj->summary) {
            $tpl->title_template = $this->setup_widget($obj->title);
            $tpl->updated = $obj->updated;
            $tpl->posts = $obj->posts;
            if (is_null($obj->firstposter)) {
                $tpl->firstposter_template = $this->setup_widget(new RegularText('User Deleted'));
            } else {
                $tpl->firstposter_template = $this->setup_widget($obj->firstposter);
            }
            if (is_null($obj->lastposter)) {
                $tpl->lastposter_template = $this->setup_widget(new RegularText('User Deleted'));
            } else {
                $tpl->lastposter_template = $this->setup_widget($obj->lastposter);
            }
        } else if ($obj instanceof WidgetGroup) {
            $tpl->contents = array_map(array($this, 'setup_widget'), $obj->contents);
            if ($obj instanceof WidgetList) {
                foreach($tpl->contents as $i => $t) {
                    if ($i % 2 == 0) {
                        $class = 'even';
                    } else {
                        $class = 'odd';
                    }
                    if ($i == 0) {
                        $class.= ' first';
                    }
                    if ($i == count($obj->contents) - 1) {
                        $class.= ' last';
                    }
                    $t->list_attributes = $class;
                }
            }
            if ($obj instanceof Form) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'form')) . ' enctype="multipart/form-data"';
                $tpl->method = strtolower($obj->method);
                $tpl->action = $obj->action;
                if ($obj instanceof EditBox) {
                    $tpl->rows = $obj->rows;
                    $tpl->columns = $obj->columns;
                    $tpl->text = $obj->text->message;
                    $tpl->submitable = $obj->submitable;
                    //TODO This line should be the responsibility of edit.php, not this object
                    $tpl->otherinputs_template = $this->setup_widget(new HiddenInput('part', 1));
		    $tpl->button_template = $this->setup_widget(new SubmitInput('Change Plan'));
                }
            } else if ($obj instanceof FormItemSet) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
                $tpl->title = $obj->title;
            } else if ($obj instanceof AutoRead) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
                $tpl->level_link_template = $this->setup_widget($obj->link);
                $tpl->markasread_template = $this->setup_widget($obj->markasread_link);
            } else if ($obj instanceof NotesBoard) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            } else if ($obj instanceof NotesTopic) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
                $tpl->title_template = $this->setup_widget($obj->title);
            } else if ($obj instanceof WidgetList) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            } else if ($obj instanceof WidgetGroup) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            }
        } else if ($obj instanceof SubmitInput) {
            if ($obj->name) {
                $tpl->tag_attributes.= " name=\"$obj->name\"";
            }
            $tpl->text = $obj->value;
        } else if ($obj instanceof FormItem) {
            if ($obj->identifier) {
                $item_id = $obj->identifier;
            } else {
                $item_id = $obj->parent_form->identifier . '_' . $obj->name;
            }
            $item_id = str_replace('[]', '', $item_id);
            // If it's possible there are multiple inputs with the same name,
            // append a number to the end to make it unique
            if ($obj instanceof CheckboxInput || $obj instanceof RadioInput) {
                if (!isset($this->element_ids[$item_id])) $this->element_ids[$item_id] = 0;
                $num = (int)$this->element_ids[$item_id]++;
                $item_id = $item_id . $num;
            }
            $tpl->label = $obj->title;
            $tpl->description = $obj->description;
            $tpl->prompt_id = $item_id;
            $tpl->type = $obj->type;
            $tpl->name = $obj->name;
            $tpl->value = $obj->value;
            $tpl->disabled = $obj->disabled;
            if (isset($obj->readonly)) {
                $tpl->readonly = $obj->readonly;
            }
            if (isset($obj->checked)) {
                $tpl->checked = $obj->checked;
            }
            if (isset($obj->rows)) {
                $tpl->rows = $obj->rows;
            }
            if (isset($obj->cols)) {
                $tpl->cols = $obj->cols;
            }
        } else if ($obj instanceof Hyperlink) {
            $tpl->href = htmlentities(html_entity_decode($obj->href));
            $tpl->description = $obj->description;
            $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            $tpl->setTemplate('views/templates/std/Hyperlink.tpl.php');
            if ($obj instanceof DisplayToggleLink) {
                $tpl->onclick = "toggleShowHide('$obj->target_id', this, '$obj->show_desc', '$obj->hide_desc')";
                if ($obj->initially_hidden) {
                    $tpl->js = "document.getElementById('$obj->target_id').style.display = 'none';";
                }
                $tpl->setTemplate('views/templates/std/DisplayToggleLink.tpl.php');
            }
        } else if ($obj instanceof Secret) {
            $tpl->date = $obj->date;
            $tpl->secret_id = $obj->secret_id;
            $tpl->message = $obj->message;
        } else if ($obj instanceof PlanContent) {
            $tpl->username = $obj->username;
            $tpl->lastupdate = $obj->lastupdate;
            $tpl->lastlogin = $obj->lastlogin;
            $tpl->planname = $obj->planname;
            $tpl->plan_template = $this->setup_widget($obj->text);
            if ($obj->addform) {
                $tpl->addform_template = $this->setup_widget($obj->addform);
            }
        } else if ($obj instanceof PlanText) {
            $tpl->text = $obj->message;
        } else if ($obj instanceof HeadingText) {
            $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            $tpl->tag = 'h' . $obj->sublevel;
            $tpl->text = $obj->message;
            $tpl->setTemplate('views/templates/std/GenericTag.tpl.php');
        } else if ($obj instanceof RegularText) {
            $tpl->tag = 'span';
            $tpl->text = $obj->message;
            $tpl->setTemplate('views/templates/std/GenericTag.tpl.php');
        } else if ($obj instanceof Text) {
            $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
            $tpl->title = $obj->title;
            $tpl->message = $obj->message;
        } else if ($obj instanceof NotesPost) {
            $tpl->post_id = $obj->id;
            $tpl->date = $obj->date;
            $tpl->post_author_template = $this->setup_widget($obj->poster);
            $tpl->score = $obj->score;
            $tpl->user_vote = $obj->user_vote;
            $tpl->votes = $obj->votes;
            $tpl->text = $obj->contents;
        } else if ($obj instanceof NotesNavigation) {
            $tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'notes_nav'));
            if ($obj->newest instanceof Hyperlink) {
                $tpl->newest = $this->setup_widget($obj->newest);
                $tpl->newest->description = '&lt;&lt;';
                $tpl->navigable['newest'] = true;
            } else {
                $tpl->newest = new Plans_Savant3();
                $tpl->newest->setTemplate('views/templates/std/GenericTag.tpl.php');
                $tpl->newest->text = '&lt;&lt;';
                $tpl->newest->tag = 'span';
                $tpl->navigable['newest'] = false;
            }
            foreach(array('even_newer', 'newer', 'current', 'older', 'even_older') as $linkname) {
                if ($obj->$linkname instanceof Hyperlink || $linkname == 'current') {
                    $tpl->$linkname = $this->setup_widget($obj->$linkname);
                    $tpl->navigable[$linkname] = true;
                } else {
                    $tpl->$linkname = new Plans_Savant3();
                    $tpl->$linkname->setTemplate('views/templates/std/GenericTag.tpl.php');
                    $tpl->$linkname->text = '_';
                    $tpl->$linkname->tag = 'span';
                    $tpl->navigable[$linkname] = false;
                }
            }
            if ($obj->oldest instanceof Hyperlink) {
                $tpl->oldest = $this->setup_widget($obj->oldest);
                $tpl->oldest->description = '&gt;&gt;';
                $tpl->navigable['oldest'] = true;
            } else {
                $tpl->oldest = new Plans_Savant3();
                $tpl->oldest->setTemplate('views/templates/std/GenericTag.tpl.php');
                $tpl->oldest->text = '&gt;&gt;';
                $tpl->oldest->tag = 'span';
                $tpl->navigable['oldest'] = false;
            }
        }
        return $tpl;
    }
}
/**
 * Holds a name of the interface object that we're using for this particular interface.
 *
 * For now, every interface file must implement this.
 */
global $my_interface_name;
$my_interface_name = 'BaseInterface';
/**
 * This function gets called to help build the interface, based on the global variable.
 *
 * @todo At some point, get rid of this (probably by changing how interfaces are stored in the DB)
 *
 * @return DisplayInterface
 */
function interface_construct() {
    global $my_interface_name;
    return new $my_interface_name();
}
?>
