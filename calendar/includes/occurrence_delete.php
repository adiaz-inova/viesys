<?php
/*
 * Copyright 2010 Sean Proctor
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if ( !defined('IN_PHPC') ) {
       die("Hacking attempt");
}

function occurrence_delete()
{
	global $vars, $phpcdb;

	$html = tag('div', attributes('class="phpc-container"'));

	if(empty($vars["oid"])) {
		$message = _('No occurrence selected.');
		$html->add(tag('p', $message));
		return $html;
	}

	if (is_array($vars["oid"])) {
		$oids = $vars["oid"];
	} else {
		$oids = array($vars["oid"]);
	}

	if (empty($vars["confirm"])) {
		$list = tag('ul');
		foreach ($oids as $oid) {
			$occur = $phpcdb->get_occurrence_by_oid($oid);
			$list->add(tag('li', "$oid: \"" . $occur->get_subject()
						. "\" " . _("at") . " " .
						$occur->get_date_string()));
		}
		$html->add(tag('p', _('Confirm you want to delete:')));
		$html->add($list);
		$html->add(" [ ", create_action_link(_('Confirm'),
					"occurrence_delete", array("oid" => $oids,
						"confirm" => "1")), " ] ");
		$html->add(" [ ", create_action_link(_('Deny'),
					"display_month"), " ] ");
		return $html;
	}

	$removed_occurs = array();
	$unremoved_occurs = array();
	$permission_denied = array();

	foreach($oids as $oid) {
		$occur = $phpcdb->get_occurrence_by_oid($oid);
		if(!can_modify_event($occur)) {
			$permission_denied[] = $oid;
		} else {
			if($phpcdb->delete_occurrence($oid)) {
				$removed_occurs[] = $oid;
			} else {
				$unremoved_occurs[] = $oid;
			}
		}
	}

	if(sizeof($removed_occurs) > 0) {
		if(sizeof($removed_occurs) == 1)
			$text = _("Removed occurrence");
		else
			$text = _("Removed occurrences");
		$text .= ': ' . implode(', ', $removed_occurs);
		$html->add(tag('p', $text));
	}

	if(sizeof($unremoved_occurs) > 0) {
		if(sizeof($unremoved_occurs) == 1)
			$text = _("Could not remove occurrence");
		else
			$text = _("Could not remove occurrences");
		$text .= ': ' . implode(', ', $unremoved_occurs);
		$html->add(tag('p', $text));
	}

	if(sizeof($permission_denied) > 0) {
		if(sizeof($permission_denied) == 1)
			$text = _("You do not have permission to remove the occurrence.");
		else
			$text = _("You do not have permission to remove occurrences.");
		$text .= ': ' . implode(', ', $permission_denied);
		$html->add(tag('p', $text));
	}
	
        return $html;
}

?>
