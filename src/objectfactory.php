<?php
class ObjectFactory {
	static public function get(SimpleXMLElement $obj){
		$id = $obj['id'];
		$type = $obj['type'];
		$x = $obj['x'];
		$y = $obj['y'];
		$r = $obj['r'];
		$s = $obj['s'];
		$class = $obj['class'];
		/*if($type == 'invisible'){
			return new InvisibleObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		else*/if($type == "text"){
			return new TextObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		elseif($type == "shape"){
			return new ShapeObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		elseif($type == "persistentgroup"){
			return new PersistentGroupObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		elseif($type == "image"){
			return new ImageObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		elseif($type == "button"){
			return new ButtonObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
		else{
			return new PreziObject($id, $type, $x, $y, $r, $s, $class, $obj);
		}
	}
}
?> 
