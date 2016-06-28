<?php
define('DATE_FORMAT', 'D F jS Y, g:i A');
require_once ('lib/savant/Savant3.php');
require_once ('interfaces/base.php');
/**
 * "Modern" Interface
 * The default Plans interface. Table-icious. Ick.
 */
class LegacyDefaultInterface extends BaseInterface {
    protected $page;
    public function setup_page(PlansPage $page) {
        $this->page = $page;
        $tpl = new Plans_Savant3();
        $tpl->page_title = $page->title;
        $tpl->stylesheets = $page->stylesheets;
        array_unshift($tpl->stylesheets, 'styles/legacy_globals.css');
        $tpl->scripts = $this->get_local_jsfiles($page);
        $tpl->body_id = 'planspage_' . strtolower($page->identifier);
        $tpl->body_class = strtolower($page->group);
        if ($page->group == 'Preferences' && $page->identifier != 'blocks' && $page->identifier != 'autoreadedit') {
            $tpl->center = true;
        } else {
            $tpl->center = false;
        }
        $tpl->mainpanel_template = $this->setup_mainpanel($page);
        $tpl->contents = array_map(array($this, 'setup_widget'), $page->contents);
        $tpl->footer_template = $this->setup_footer($page->footer);
        $tpl->setTemplate('views/templates/legacy/PlansPage.tpl.php');
        return $tpl;
    }
    protected function setup_mainpanel(PlansPage $page) {
        $tpl = new Plans_Savant3();
        $panel = $page->mainpanel;
        $tpl->panel = $panel;
        $tpl->links_template = $this->setup_links($panel->links);
        $tpl->autoread_template = $this->setup_autoreads($panel->autoreads, $page->autoreadpriority);
        $tpl->setTemplate('views/templates/legacy/Mainpanel.tpl.php');
        return $tpl;
    }
    protected function setup_links(WidgetList $links) {
        $tpl = $this->setup_widget($links);
        foreach($tpl->contents as $t) {
            $t->description = strtolower($t->description);
            $t->tag_attributes = ' class="main"';
        }
        $tpl->setTemplate('views/templates/legacy/Links.tpl.php');
        return $tpl;
    }
    protected function setup_autoreads(WidgetList $autoreads = NULL, $lvl) {
        if (!$autoreads) {
            return false;
        }
        $tpl = new Plans_Savant3();
        foreach($autoreads->contents as $ar) {
            $t = new Plans_Savant3();
            $tpl->contents[] = $t;
            $t->level_link = $ar->link;
            $t->markasread_link = $ar->markasread_link;
            if ($ar->priority == $lvl) {
                $t->current = true;
            } else {
                $t->current = false;
            }
            $t->priority = $ar->priority;
            $t->names = $ar->contents;
            $t->setTemplate('views/templates/legacy/AutoRead.tpl.php');
        }
        $tpl->setTemplate('views/templates/legacy/AutoReads.tpl.php');
        return $tpl;
    }
    protected function setup_footer($footer) {
        $tpl = parent::setup_footer($footer);
        $tpl->setTemplate('views/templates/legacy/Footer.tpl.php');
        return $tpl;
    }
    protected function get_local_jsfiles($page) {
        $arr = parent::get_local_jsfiles($page);
        switch ($page->identifier) {
            case 'board_messages':
                $arr[] = 'js/board_voting.js';
            break;
        }
        return $arr;
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
        $tpl = parent::setup_widget($obj);
        if ($obj instanceof NotesTopic && $obj->summary) {
            // Override the forced lowercase that gets applied to Hyperlinks
            $tpl->title_template->description = $obj->title->description;
            $tpl->setTemplate('views/templates/legacy/NotesBoardTopic.tpl.php');
        } else if ($obj instanceof WidgetGroup) {
            if ($obj instanceof Form) {
                foreach($obj->contents as $i => $item) {
                    if ($item instanceof SubmitInput) {
                        $tpl->submit_button = $item;
                    }
                }
                $tpl->setTemplate('views/templates/legacy/Form.tpl.php');
                if ($this->page->identifier == 'planname' || $this->page->identifier == 'search') {
                    $tpl = $this->oneline_form($obj, $tpl);
                } else if ($obj->identifier == 'signup') {
                    $tpl->contents[2]->setTemplate('views/templates/legacy/FormElement_no_table.tpl.php');
                } else if ($this->page->identifier == 'poll') {
                    $tpl->setTemplate('views/templates/legacy/PollForm.tpl.php');
                }
                if ($obj instanceof EditBox) {
                    $tpl->setTemplate('views/templates/legacy/EditBox.tpl.php');
                }
            } else if ($obj instanceof FormItemSet) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
                foreach($tpl->contents as $template) {
                    $template->setTemplate('views/templates/legacy/FormElement_no_row.tpl.php');
                }
                $tpl->setTemplate('views/templates/legacy/FormItemSet.tpl.php');
            } else if ($obj instanceof NotesBoard) {
                foreach($tpl->contents as $t) {
                    $t->list_attributes = str_replace('even', 'noteslight', $t->list_attributes);
                    $t->list_attributes = str_replace('odd', 'notesdark', $t->list_attributes);
                }
                $tpl->setTemplate('views/templates/legacy/NotesBoard.tpl.php');
            } else if ($obj instanceof NotesTopic) {
                $tpl->tag_attributes = ' class="boardmessages"';
                foreach($tpl->contents as $t) {
                    $t->list_attributes = str_replace('even', 'noteslight', $t->list_attributes);
                    $t->list_attributes = str_replace('odd', 'notesdark', $t->list_attributes);
                }
                $tpl->setTemplate('views/templates/legacy/NotesTopic.tpl.php');
            } else if ($obj instanceof WidgetList) {
                $tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
                $tpl->setTemplate('views/templates/legacy/WidgetList.tpl.php');
                // The Preferences page
                if ($this->page->identifier == 'prefs') {
                    $part2 = false;
                    foreach($obj->contents as $i => $item) {
                        $t = $tpl->contents[$i];
                        if ($part2) {
                            $t->tag_attributes = ' class="lev2"';
                        } else if ($item instanceof HeadingText) {
                            $t->tag_attributes = ' class="main"';
                            $t->tag = 'p';
                            $t->setTemplate('views/templates/std/GenericTag.tpl.php');
                            $part2 = true;
                        } else {
                            $t->tag_attributes = ' class="main"';
                        }
                    }
                }
                // The autoread management pages
                else if ($obj->identifier == 'autoread_alphabet') {
                    $tpl->setTemplate('views/templates/legacy/WidgetGroup.tpl.php');
                } else if ($obj->identifier == 'search_results' || $obj->group == 'result_sublist') {
                    blkafkjaass;
                    $tpl->setTemplate('views/templates/tableless/WidgetList.tpl.php');
                }
            } else if ($obj instanceof WidgetGroup) {
                $tpl->setTemplate('views/templates/legacy/WidgetGroup.tpl.php');
                if ($obj->group == 'notes_header') {
                    $t = $tpl;
                    $tpl = new Plans_Savant3();
                    $tpl->inner_template = $t;
                    $tpl->tag = 'center';
                    $tpl->setTemplate('views/templates/std/GenericWrapperTag.tpl.php');
                }
            }
            return $tpl; //TODO remove
            
        } else if ($obj instanceof SubmitInput) {
            // We handle this elsewhere, so print nothing here
            $tpl->setTemplate('views/templates/std/Empty.tpl.php');
        } else if ($obj instanceof FormItem) {
            if ($obj instanceof HiddenInput) {
                $tpl->setTemplate('views/templates/std/FormInput.tpl.php');
            } else if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
                $tpl->setTemplate('views/templates/legacy/FormElement_title_prepend.tpl.php');
            } else {
                $tpl->setTemplate('views/templates/legacy/FormElement.tpl.php');
            }
        } else if ($obj instanceof Hyperlink) {
            $tpl->description = strtolower($obj->description);
            if ($obj->identifier == 'older_secrets') {
                $t = $tpl;
                $tpl = new Plans_Savant3();
                $tpl->inner_template = $t;
                $tpl->tag = 'p';
                $tpl->setTemplate('views/templates/std/GenericWrapperTag.tpl.php');
            }
        } else if ($obj instanceof Secret) {
            $tpl->message = preg_replace('/(^<p class="sub">)|(<\/p>$)/', '', $tpl->message);
            $tpl->setTemplate('views/templates/legacy/Secret.tpl.php');
        } else if ($obj instanceof PlanContent) {
            if ($obj->addform) {
                $tpl->addform_present = true;
                $tpl->addform_template = $this->oneline_form($obj->addform, $tpl->addform_template);
                $tpl->addform_template->setTemplate('views/templates/legacy/addform.tpl.php');
            } else {
                $tpl->addform_present = false;
            }
            $tpl->setTemplate('views/templates/legacy/Plan.tpl.php');
        } else if ($obj instanceof PlanText) {
            $tpl->setTemplate('views/templates/legacy/PlanText.tpl.php');
        } else if ($obj instanceof HeadingText) {
            $tpl->tag = 'h' . ($obj->sublevel + 1);
            $tpl->text = $obj->message;
            $tpl->setTemplate('views/templates/std/GenericTag.tpl.php');
        } else if ($obj instanceof RegularText) {
            $tpl->tag = 'span';
            $tpl->text = $obj->message;
            $tpl->setTemplate('views/templates/std/GenericTag.tpl.php');
        } else if ($obj instanceof Text) {
            $tpl->setTemplate('views/templates/legacy/Text.tpl.php');
        } else if ($obj instanceof NotesPost) {
            $tpl->text = preg_replace('/(^<p class="sub">)|(<\/p>$)/', '', $tpl->text);
            $tpl->yes_style = ($obj->user_vote == 'yes' ? ' style="border:#222222 thin solid"' : null);
            $tpl->no_style = ($obj->user_vote == 'no' ? ' style="border:#222222 thin solid"' : null);
            $tpl->setTemplate('views/templates/legacy/NotesPost.tpl.php');
        } else if ($obj instanceof NotesNavigation) {
            $tpl->current->text = '[' . $tpl->current->text . ']';
            $tpl->setTemplate('views/templates/legacy/NotesNavigation.tpl.php');
        }
        return $tpl;
    }
    /**
     * Consistency be damned, we're putting these forms on one line!
     *
     * Yee-haw!
     */
    private function oneline_form($form, $form_tpl) {
        $inputs = array();
        foreach($form->contents as $o) {
            if ($o instanceof SubmitInput) {
                // do nothing
                
            } else if ($o instanceof FormItem) {
                $inputs[] = $o;
            } else if ($o instanceof FormItemSet) {
                foreach($o->contents as $_o) $inputs[] = $_o;
            }
        }
        $form_tpl->inputs = $inputs;
        $form_tpl->setTemplate('views/templates/legacy/Form_oneline.tpl.php');
        return $form_tpl;
    }
}
global $my_interface_name;
$my_interface_name = 'LegacyDefaultInterface';
