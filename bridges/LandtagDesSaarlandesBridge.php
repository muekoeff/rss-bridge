<?php
class LandtagDesSaarlandesBridge extends BridgeAbstract {

	const MAINTAINER = 'µKöff';
	const NAME = 'Landtag des Saarlandes';
	const URI = 'https://www.landtag-saar.de/';
	const DESCRIPTION = 'Aktuelles des Landtages des Saarlandes';

	const PARAMETERS = array( array(
		'feed' => array(
			'name' => 'Feed',
			'type' => 'list',
			'required' => true,
			'values' => array(
				'Mitteilungen' => 'mitteilungen',
				'Kunst im Landtag' => 'kunst',
				'Reden' => 'reden',
				'Bilder' => 'bilder'
			)
		)
	));

	public function collectData(){
		$html = getSimpleHTMLDOM(static::URI . 'aktuelles/') or returnServerError('No contents received!');
		$newsContainer = $html->find('.NewsAll', $this->feedNameToIndex($this->getInput('feed')));

		foreach($newsContainer->find('.swiper-wrapper .swiper-slide') as $swiperSlide) {
			foreach($swiperSlide->find('.row') as $row) {
				$this->items[] = $this->collectArticle($row);
			}
		}
	}

	private function collectArticle($element) {
		$item = array();
		$item['uri'] = self::URI . $element->find('.SitzungsInfo a[href]', 0)->href;
		$item['title'] = $element->find('.Inhalt', 0)->plaintext;
		$item['timestamp'] = DateTime::createFromFormat('!d.m.y', $element->find('.Datum', 0)->plaintext)->getTimestamp();
		$item['author'] = static::NAME;
		return $item;
	}

	private function feedNameToIndex($feedName) {
		switch($feedName) {
			case 'mitteilungen':
				return 0;
			case 'kunst':
				return 1;
			case 'reden':
				return 2;
			case 'bilder':
				return 3;
			default:
				returnServerError('Unknown feed name!');
		}
	}

	public function getIcon(){
		return self::URI . '/favicon.ico';
	}

	public function getName(){
		$name = static::NAME;
		switch($this->getInput('feed')) {
			case 'mitteilungen':
				$name .= ' — Mitteilungen';
				break;
			case 'kunst':
				$name .= ' — Kunst';
				break;
			case 'reden':
				$name .= ' — Reden';
				break;
			case 'bilder':
				$name .= ' — Bilder';
				break;
		}
		return $name;
	}
}
