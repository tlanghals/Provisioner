<?php

/**
 * Cisco SPA Phone File
 *
 * @author Andrew Nagy
 * @license MPL / GPLv2 / LGPL
 * @package Provisioner
 */
class endpoint_cisco_spa5xx_phone extends endpoint_cisco_base {

    public $family_line = 'spa5xx';

    function parse_lines_hook($line_data) {
        $line = $line_data['line'];
        if (strlen($line_data['displayname']) > 12) {
            $short_name = substr($line_data['displayname'], 0, 8) . "...";
        } else {
            $short_name = $line_data['displayname'];
        }
        if ((isset($line_data['secret'])) && ($line_data['secret'] != "")) {
            $line_data['dial_plan'] = $this->settings['dial_plan'];
        } else {
            $line_data['dial_plan'] = "";
        }
        if (isset($this->settings['loops']['lineops'][$line])) {
            
            $line_data['displaynameline'] = str_replace('{$count}', $line_data['count'], $this->settings['loops']['lineops'][$line]['displaynameline']);
            $line_data['short_name'] = str_replace('{$count}', $line_data['count'], $short_name);
            
            if (($this->settings['loops']['lineops'][$line]['keytype'] == "blf") AND ($this->settings['loops']['lineops'][$line]['blfext'] != "")) {
                $line_data['username'] = $this->settings['loops']['lineops'][$line]['blfext'];
                $line_data['secret'] = 'n/a';
                $line_data['blf_ext_type'] = "Disabled";
                $line_data['share_call_appearance'] = "shared";
                $line_data['extended_function'] = "fnc=blf+sd+cp;sub=" . $this->settings['loops']['lineops'][$line]['blfext'] . "@" . $this->settings['line'][0]['server_host'];
            } elseif (($this->settings['loops']['lineops'][$line]['keytype'] == "sd") AND ($this->settings['loops']['lineops'][$line]['blfext'] != "")) {
                $line_data['username'] = $this->settings['loops']['lineops'][$line]['blfext'];
                $line_data['secret'] = 'n/a';
                $line_data['blf_ext_type'] = "Disabled";
                $line_data['share_call_appearance'] = "shared";
                $line_data['extended_function'] = "fnc=sd;sub=" . $this->settings['loops']['lineops'][$line]['blfext'] . "@" . $this->settings['line'][0]['server_host'];
            } elseif($this->settings['loops']['lineops'][$line]['keytype'] == "disabled") {
                $line_data['blf_ext_type'] = "Disabled";
            } else {
                if (!isset($line_data['secret'])) {
                    $line_data['displaynameline'] = $this->lines[1]['options']['displaynameline'];
                    $line_data['short_name'] = $this->lines[1]['options']['short_name'];
                    $line_data['username'] = '';
                    $line_data['secret'] = '';
                    $line_data['blf_ext_type'] = "1";
                    $line_data['share_call_appearance'] = "private";
                    $line_data['extended_function'] = "";
                } else {
                    $line_data['blf_ext_type'] = $line;
                    $line_data['share_call_appearance'] = "private";
                    $line_data['extended_function'] = "";
                }
            }
        } else {
            $line_data['displaynameline'] = $line_data['displayname'];
            $line_data['short_name'] = $short_name;
            $line_data['blf_ext_type'] = "1";
            $line_data['share_call_appearance'] = "private";
            $line_data['extended_function'] = "";
        }
        return($line_data);
    }

    function prepare_for_generateconfig() {
        parent::prepare_for_generateconfig();
        
        for($i = 0; $i <= $this->max_lines-1; $i++) {
            if(!isset($this->settings['line'][$i]['line'])) {
                $this->settings['line'][$i]['line'] = $i+1;
            }
        }

        if (isset($this->settings['loops']['unit1'])) {
            foreach ($this->settings['loops']['unit1'] as $key => $data) {
                if ($this->settings['loops']['unit1'][$key]['data'] == '') {
                    unset($this->settings['loops']['unit1'][$key]);
                }
                if (($this->settings['loops']['unit1'][$key]['data'] != '') && ($this->settings['loops']['unit1'][$key]['keytype'] == 'blf')) {
                    $temp_ext = $this->settings['loops']['unit1'][$key]['data'];
                    $this->settings['loops']['unit1'][$key]['data'] = "fnc=blf+sd+cp;sub=" . $temp_ext . "@" . $this->server[1]['ip'];
                }
            }
        }

        if (isset($this->settings['loops']['unit2'])) {
            foreach ($this->settings['loops']['unit2'] as $key => $data) {
                if ($this->settings['loops']['unit2'][$key]['data'] == '') {
                    unset($this->settings['loops']['unit2'][$key]);
                }
                if (($this->settings['loops']['unit2'][$key]['data'] != '') && ($this->settings['loops']['unit2'][$key]['keytype'] == 'blf')) {
                    $temp_ext = $this->settings['loops']['unit2'][$key]['data'];
                    $this->settings['loops']['unit2'][$key]['data'] = "fnc=blf+sd+cp;sub=" . $temp_ext . "@" . $this->server[1]['ip'];
                }
            }
        }
    }

}