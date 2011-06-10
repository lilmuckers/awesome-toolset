<?php
namespace Base\SimpleXML;

class Element extends \SimpleXMLElement
{
	/**
	 * Formatting constants for the nice output function
	 */
	const INDENT_WIDTH	= 4;
	const LINE_BREAK	= "\n";

	/**
	 * Simple boolean return text for children
	 *
	 * @return boolean
	 */
	public function hasChildren()
	{
		if (!$this->children()) {
			return false;
		}

		// simplexml bug: @attributes is in children() but invisible in foreach
		foreach ($this->children() as $k=>$child) {
			return true;
		}
		return false;
	}

	/**
	 * Create the xml entities for merging
	 *
	 * @param	string
	 * @return string
	 */
	public function xmlEntities($value = null)
	{
		if (is_null($value)) {
			$value = $this;
		}
		$value = (string)$value;
		
		$search = array('&', '"', "'", '<', '>');
		$replace = array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;');
		$value = str_replace($search, $replace, $value);

		return $value;
	}
	
	/**
	 * Merge a new file into the existing xml structure
	 * 
	 * If $overwrite is false then it will merge only missing nodes,
	 * and not, as it happens, overwrite anything - shock!
	 * 
	 * @param string $filename
	 * @param bool $overwrite
	 * @return \Base\SimpleXML\Element
	 */
	public function addFile($filename, $overwrite = false)
	{
		if(!is_readable($filename)){
			throw new \Base\Exception\SimpleXML("This is either not a valid filename, or not a extant file.");
		}
		
		//create the simplexml element for this file - using this class, obviously.
		$toMerge = simplexml_load_file($filename, get_class($this));
		$this->mergeXml($toMerge, $overwrite);
		
		return $this;
	}
	
	/**
	 * Initiate the merge
	 * 
	 * @param \Base\SimpleXML\Element $xml
	 * @param bool $overwrite
	 * @return \Base\SimpleXML\Element
	 */
	public function mergeXml(\Base\SimpleXML\Element $xml, $overwrite = false)
	{
		//now we iterate through the children and merge them into the structure
		foreach($xml->children() as $child){
			$this->extendChildNode($child, $overwrite);
		}
		return $this;
	}
	
	/**
	 * Merge a source child node into this parents child node.
	 * 
	 * @param \Base\SimpleXML\Element $source
	 * @param bool $overwrite
	 * @return \Base\SimpleXML\Element
	 */
	public function extendChildNode(Element $source, $overwrite = false)
	{
		//we need to know the target we'll be merging into. Placeholder!
		$targetNode = null;
		
		//name of the child node we're merging into
		$childNodeName = $source->getName();
		
		//if this is a simple node with no children, simple to merge
		if(!$source->hasChildren()) {
			//does this node have a child of the same name
			if($this->hasMatchingChild($source)) {
				//if the target has children, we'll lose a buttload of data if we just merge
				if($this->$childNodeName->hasChildren()) {
					return $this;
				}
				//if we're overwriting, we want rid of this node
				if($overwrite) {
					unset($this->$childNodeName);
				} else {
					return $this;
				}
			}
			
			//now we put the node into the tree - with attributes;
			$targetNode = $this->addChildNode($source);
			return $this;
		}
		
		//now we need to modify new/existing children and grandchildren
		if($this->hasMatchingChild($source)) {
			$targetNode = $this->$childNodeName;
		} else {
			//create a new child node if there isn't an extant one
			$targetNode = $this->addChildNode($source);
		}
		
		//wash/rinse/repeat for the children.
		foreach($source->children() as $childKey=>$childNode) {
			$targetNode->extendChildNode($childNode, $overwrite);
		}
		
		return $this;
	}
	
	/**
	 * Check if this has a child to match the source by name attribtue as well
	 * 
	 * @param \Base\SimpleXML\Element
	 * @return bool
	 */
	public function hasMatchingChild($source)
	{
		$childNodeName	= (string) $source->getName();
		
		//inital check if child exists, then if name value is set
		if(!isset($this->$childNodeName)){
			return false;
		}
		
		//pull out the name values - and check they're both set
		$childName		= (string) $this->$childNodeName->attributes()->name;
		$sourceName		= (string) $source->attributes()->name;
		if(!$childName && !$sourceName){
			if(!$this->$childNodeName->hasChildren() && !$source->hasChildren()){
				return false;
			}
			return true;
		}
		
		//check by name
		$nameAttr = null;
		if($childName == $sourceName){
			return true;
		}
		return false;
	}
	
	/**
	 * Add a child node to this tree
	 * 
	 * @param \Base\SimpleXML\Element $source
	 * @return \Base\SimpleXML\Element
	 */
	public function addChildNode($source)
	{
		//now we put the node into the tree - with attributes;
		$targetNode = $this->addChild($source->getName(), $source->xmlEntities());
		foreach($source->attributes() as $key=>$value) {
			$targetNode->addAttribute($key, $this->xmlEntities($value));
		}
		
		return $targetNode;
	}

	/**
	 * Makes nicely formatted XML from the node
	 * 
	 * @param string $filename
	 * @param int|boolean $level if false
	 * @return string
	 */
	public function asNiceXml($filename = null, $level = 0)
	{
		//first we build the padding for the level in question
		$indent = str_pad('', $level * self::INDENT_WIDTH, ' ', STR_PAD_LEFT);
		
		//start the tag
		$_xml = sprintf("%s<%s", $indent, $this->getName());
		
		//give the tag its attributes
		if ($attributes = $this->attributes()) {
			foreach ($attributes as $key=>$value) {
				//ensure we escape any quotation marks
				$value = str_replace('"', '\"', (string) $value);
				$_xml .= sprintf(' %s="%s"', $key, $value);
			}
		}
		
		//now we iterate through onto the child nodes
		if ($this->hasChildren()) {
			//this item has child nodes, we need to render them
			$_xml .= '>' . self::LINE_BREAK;
			foreach ($this->children() as $child) {
				$_xml .= $child->asNiceXml(null, $level + 1);
			}
			$_xml .= sprintf("%s</%s>%s", $indent, $this->getName(), self::LINE_BREAK);
		} else {
			$value = (string) $this;
			if (strlen($value) > 0) {
				//this item has only a text node - render it
				$value = $this->xmlEntities($value);
				$_xml .= sprintf("><![CDATA[%s]]></%s>%s", $value, $this->getName(), self::LINE_BREAK);
			} else {
				//otherwise, just close the tag
				$_xml .= sprintf('/>%s', self::LINE_BREAK);
			}
		}
		
		//if it was asked for, and it is appropriate - output to the given file
		if ((0 === $level || false === $level) && !is_null($filename)) {
			file_put_contents($filename, $_xml);
		}

		return $_xml;
	}
}