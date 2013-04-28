<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Let the Zopim's API do its own work.
 *
 * @package   block_zopim
 * @copyright 2013 onwards Matteo Scaramuccia <moodle@matteoscaramuccia.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_zopim extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_zopim');
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_config() {
        return true;
    }

    public function applicable_formats() {
        return array(
            'all' => true
        );
    }

    public function specialization() {
        $this->title = get_string('pluginname', 'block_zopim');
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $CFG, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        if (!isset($this->config)) {
            $this->config = new stdClass();
        }

        $zopim_widget_id = $CFG->block_zopim_widgetid;

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $userfullname = s(get_string('zopimdefuserfullname', 'block_zopim'));
        $useremail = s(get_string('zopimdefuseremail', 'block_zopim'));
        $userlanguage = $CFG->lang;

        if (isloggedin() && !isguestuser()) {
            $userfullname = s(fullname($USER));
            $useremail = s($USER->email);
            $userlanguage = s($USER->lang);
        }

        $zopimchecking = s(get_string('zopimchecking', 'block_zopim'));
        $zopimoffline = s(get_string('zopimoffline', 'block_zopim'));
        $zopimonline = s(get_string('zopimonline', 'block_zopim'));

        $this->content->text = <<<EOT

<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
// <![CDATA[
window.\$zopim||(function(d,s){var z=\$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//cdn.zopim.com/?$zopim_widget_id';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
// ]]>
</script>
<!--End of Zopim Live Chat Script-->
<p>
<a id="zopim_chat_status_a"
    style="text-decoration: none !important; cursor: pointer !important; color: black;"
    onclick="\$zopim.livechat.window.toggle(); return false;"
    title="$zopimchecking"><img
    id="zopim_chat_status_img"
    style="display: block; margin-left: auto; margin-right: auto;"
    src="$CFG->wwwroot/blocks/zopim/pix/zopim-logo.png"
    alt="$zopimchecking" /></a>
</p>
<script>
// <![CDATA[
    function updateZopimStatus(status) {
        var a = document.getElementById('zopim_chat_status_a');
        var img = document.getElementById('zopim_chat_status_img');
        switch (status) {
            case 'away':
            case 'online':
                a.title = '$zopimonline';
                img.alt = '$zopimonline';
                img.src = '$CFG->wwwroot/blocks/zopim/pix/online-chat-icon.png';
            break;
            case 'offline':
                a.title = '$zopimoffline';
                img.alt = '$zopimoffline';
                img.src = '$CFG->wwwroot/blocks/zopim/pix/offline-chat-icon.png';
                \$zopim.livechat.bubble.hide();
            break;
        }
    }

    \$zopim(function() {
        \$zopim.livechat.button.setHideWhenOffline();
        \$zopim.livechat
            .setName('$userfullname')
            .setEmail('$useremail')
            .setLanguage('$userlanguage')
            .setOnStatus(updateZopimStatus);
    });
// ]]>
</script>

EOT;

        return $this->content;
    }
}
