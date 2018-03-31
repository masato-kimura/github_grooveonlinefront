<?php

class View_Index_Aboutus extends \ViewModel
{
	public function view()
	{
		\Log::debug('[start]'. __METHOD__);

		$this->title_about = "当サイト『グルーヴオンライン』について";
		$this->footer_about = "team グルーヴオンライン プロジェクト<br />キムラマサト<br /><a href='mailto:info@groove-online.com?subject=お問い合わせ&amp;'>info@groove-online.com</a>";
		$this->about_grooveonline = "いま多くの人に聴かれている音楽はなんなのか？
自分が知らないだけで出会えていない素晴らしい音楽がまだあるのではないか？
ただただそのことが知りたいと思ったことがこのサイトを立ち上げるきっかけになりました。

これは私個人の感覚ではありますが、本当に良い楽曲、演奏、アーティスト、アルバムに
出会ったとき誰かにその気持ちを伝えたい衝動にかられます。
もし同じような気持ちの方がいてくれるならば
この空間を通じてその気持ちをカタチにしてみてはいかがでしょうか？
きっと私を含めそれ待っている人がいるのではないかと思います。

このグルーヴオンラインがあなたの音楽ライフにとって有意義な空間になることを願い
より良いサイトづくりに励んでいきますので、どうぞよろしくお願いいたします。";

		\Log::debug('[end]'. PHP_EOL. PHP_EOL. PHP_EOL);

		return true;
	}
}
