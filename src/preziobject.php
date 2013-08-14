<?php
class PreziObject {
	protected $id;
	protected $type;
	public $x;
	public $y;
	protected $class;
	protected $obj;
	protected $step;
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		$this->id = (string)$id;
		$this->scale = (float)$s * $GLOBALS['config']['scale'];
		$this->type = (string)$type;
		$this->x = (float)$x * $GLOBALS['config']['scale'];
		$this->y = (float)$y * $GLOBALS['config']['scale'];
		$this->rotate = (float)$r;
		$this->class = (string)$class;
		$this->obj = $obj;
		$this->step = false;
	}

	private function getAdditionalAttributes(){
		return '';
	}

	protected function getContent(){
		return '';
	}
	private function isStep(){
		if($this->step == true){
			return 'step';
		}
	}
	protected function getAdditionalCss(){
		return '';
	}
	public function __toString() {
		#scale('.$this->scale.')
		return '<div id="'.$this->id.'" class="'.$this->class.'" style="position: absolute; left: '.$this->x.'px; top:'.$this->y.';transform:scale('.$this->scale.') rotate('.$this->rotate.'deg); '.$this->getAdditionalCss().'" '.$this->getAdditionalAttributes().'>'.$this->getContent().'</div>'."\n";
	}
}
class TextObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
		$this->width = abs((float)$obj->width) / $GLOBALS['config']['scale'];
		$this->height = abs((float)$obj->height) / $GLOBALS['config']['scale'];
		$this->step = false;
	}
	protected function getAdditionalCss(){
		$ret = 'width: '.$this->width.'px; height: '.$this->height.'px;';
		if(isset($this->obj->p['align'])){
			$ret .= 'text-align: '.$this->obj->p['align'].';';
		}
		return $ret;
	}
	private function getTexts(){
		$ret = array();
		$ps = $this->obj->p;
		foreach($ps as $p){
			if(isset($p['listType'])){
				$ret[] = "<li>".$p->text."</li>";
			}
			else{
				$ret[] = $p->text;
			}
		}
		if(isset($p['listType'])){
			return '<ul>'.implode("", $ret)."</ul>";
		}
		else{
			return implode("<br/>", $ret);
		}
	}
	protected function getContent(){
		return "<p class='".$this->class."'>".$this->getTexts()."</p>";
	}
}
class InvisibleObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
		$this->width = (float)$obj->size->w / $GLOBALS['config']['scale'];
		$this->height = (float)$obj->size->h / $GLOBALS['config']['scale'];
	}
	protected function getAdditionalCss(){
		return 'width: '.$this->width.'px; height: '.$this->height.'px;';
	}
}
class ShapeObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
		$this->geom = (string)$obj->geom['type'];
	}

	protected function getContent(){
		if($this->geom == "line" || $this->geom == "arrow") {
			list($x1,$y1) = explode(" ",$this->obj->geom->sp);
			list($x2,$y2) = explode(" ", $this->obj->geom->ep);
			return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
  <line class="'.$this->geom.'" x1="'.($x1*$GLOBALS['config']['scale']).'" y1="'.($y1*$GLOBALS['config']['scale']).'" x2="'.($x2*$GLOBALS['config']['scale']).'" y2="'.($y2*$GLOBALS['config']['scale']).'"
  style="stroke:rgb(255,0,0);stroke-width:'.$this->obj->geom->t.'"/>
</svg>';
		}
		elseif($this->geom == "circle"){
			return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
  <circle class="'.$this->geom.'" cx="'.$this->obj->geom->r.'" cy="'.$this->obj->geom->r.'" r="'.$this->obj->geom->r.'" stroke="black"
  stroke-width="2" fill="none"/>
</svg> ';
		}
	}
}

class PersistentGroupObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
	}
}

class ImageObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
	}
	protected function getContent(){
		if(stristr($this->obj->resource->url,'swf')){
			$id = md5(rand(0,1000));
			return "<!--[if IE]>
<object id='".$id."' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='".($this->obj->source['w'] * $GLOBALS['config']['scale'])."' height='".($this->obj->source['h'] * $GLOBALS['config']['scale'])."'>
<param name='movie' value='repo/".$this->obj->source->url."'/>
</object>
<![endif]-->

<!--[if !IE]>-->
<object id='".$id."' type='application/x-shockwave-flash' data='repo/".$this->obj->source->url."' width='".($this->obj->source['w'] * $GLOBALS['config']['scale'])."' height='".($this->obj->source['h'] * $GLOBALS['config']['scale'])."'></object>
<!--<![endif]-->
<script type='text/javascript'>
swfobject.registerObject('".$id."', '9.0.0', false);
</script>";
		}
		else{
			return '<img src="repo/'.$this->obj->source->url.'" width="'.($this->obj->source['w'] * $GLOBALS['config']['scale']).'" height="'.($this->obj->source['h'] * $GLOBALS['config']['scale']).'" alt="" />';
		}
	}
}

class ButtonObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
		$this->width = (float)$obj->size->w / $this->scale;
		$this->height = (float)$obj->size->h / $this->scale;
	}
	protected function getAdditionalCss(){
		$ret = 'width: '.$this->width.'px; height: '.$this->height.'px;';
		if($this->obj->type == "bracket") {
			$ret .= 'border-left: 10px solid #ff0000;';
		}
		return $ret;
	}
}
?>