<?php

namespace Blocks;

class SnippetBlock
{
	protected $post;
	protected $accent_classes;
	
	public function __construct()
	{
		$this->post = block_value('post');
		
		//accent colors
		$this->accent_classes = [
			'has-theme-primary-background-color',
			//'has-theme-accent-2-background-color',
			//'has-theme-accent-3-background-color',
			//'has-theme-accent-4-background-color',
		];
	}
	
	public function getOutput()
	{
		$accentClass = $this->accent_classes[0];
		
		$html = Widget::getWidgetHtml($this->post, $accentClass);
		
		return $html;
	}
	
}
