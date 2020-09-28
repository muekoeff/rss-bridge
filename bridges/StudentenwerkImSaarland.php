<?php
class StudentenwerkImSaarlandBridge extends BridgeAbstract {

	const MAINTAINER = 'µKöff';
	const NAME = 'Studentenwerk im Saarland';
	const URI = 'https://www.studentenwerk-saarland.de/';
	const DESCRIPTION = 'Aktuelles des Studentenwerk im Saarland e.V.';

	public function collectData(){
		$html = getSimpleHTMLDOM(static::URI) or returnServerError('No contents received!');
		$newsContainer = $html->find('.articles', 0);

		foreach($newsContainer->find('article') as $article) {
			$this->items[] = $this->collectArticle($article);
		}
	}

	private function collectArticle($element) {
		$item = array();
		$item['uri'] = $element->find('.buttons-holder a', 0)->href;
		$item['title'] = $element->find('.title', 0)->plaintext;
		$item['timestamp'] = DateTime::createFromFormat('!d.m.Y', $element->find('.date', 0)->plaintext)->getTimestamp();
		$item['author'] = static::NAME;
		$item['content'] = $element->find('.text', 0)->plaintext;
		$item['enclosures'] = array(
			preg_replace('/\);/i', '', preg_replace('/^background-image: url\(\//i', self::URI, $element->find('.image-holder .image', 0)->style))
		);
		$item['categories'] = array(
			$element->find('.image-holder .label', 0)->plaintext
		);
		return $item;
	}

	public function getIcon(){
		return self::URI . '/favicon.ico';
	}
}
