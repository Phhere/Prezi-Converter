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
		$this->width = abs((float)$obj->width);
		$this->height = abs((float)$obj->height);
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
  <line class="'.$this->geom.'" x1="'.($x1).'" y1="'.($y1).'" x2="'.($x2).'" y2="'.($y2).'"
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
		copy($GLOBALS['contentPath'].'data/repo/'.$this->obj->source->url,$GLOBALS['config']['outputFolder'].'/media/'.$this->obj->source->url);
	}
	protected function getContent(){
		if(stristr($this->obj->resource->url,'swf')){
			$id = md5(rand(0,1000));
			return '<object style="width:'.($this->obj->source['w']).'px;height:'.($this->obj->source['h']).'px" data="media/'.$this->obj->source->url.'" type="application/x-shockwave-flash" >
<param name="movie" value="media/'.$this->obj->source->url.'" />
<param name="wmode" value="transparent"/>
<param name="quality" value="high"/>
</object>';
		}
		else{
			return '<img src="media/'.$this->obj->source->url.'" width="'.($this->obj->source['w']).'" height="'.($this->obj->source['h']).'" alt="" />';
		}
	}
}

class ButtonObject extends PreziObject {
	function __construct($id, $type, $x, $y, $r, $s, $class, $obj){
		parent::__construct($id, $type, $x, $y, $r, $s, $class, $obj);
		$this->width = (float)$obj->size->w;
		$this->height = (float)$obj->size->h;
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