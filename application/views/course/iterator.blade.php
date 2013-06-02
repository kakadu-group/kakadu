<?php
/**
 * 
 * @param unknown_type $data: the array of catalogs in tree structure
 * @param unknown_type $count: $counts the number of iterating to compute the indent
 * @param unknown_type $base: the base url
 * @param unknown_type $course: the course of these catalogs
 * @param unknown_type $create: the sententce "Create question" in the right language
 */
function iterate($data, $count, $base, $course, $create){
	echo "<tr><td>".blank($count)."<a href=".$base."/catalog/".$data['id'].">".$data['name']."</a></td><td>&nbsp;&nbsp;<a class='btn-small btn-primary' href=".$base."/course/".$course."/question/create?catalog=".$data['id'].">".$create."</a></td></tr>";
	if(count($data['children']) != 0){
		$count++;
		foreach($data['children'] as $children){
			iterate($children, $count, $base, $course, $create);
		}
			
	}	
}

function blank($count){
	$space = '';
	for($i = 0; $i < $count; $i++){
		$space .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	return $space;
}


?>