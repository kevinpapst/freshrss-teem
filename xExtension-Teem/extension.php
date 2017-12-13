<?php

/**
 * Class TeemExtension
 *
 * Latest version can be found at https://github.com/kevinpapst/freshrss-teem
 *
 * @author Kevin Papst
 */
class TeemExtension extends Minz_Extension
{

    /**
     * Whether the original feed content is embedded
     * @var bool
     */
    protected $showContent = false;

    /**
     * Initialize this extension
     */
    public function init()
    {
        // make sure to not run on server without libxml
        if (!extension_loaded('xml')) {
            return;
        }

        // for testing purpose you can switch to direct rendering ...
        //$this->registerHook('entry_before_display', array($this, 'embedTeemVideo'));

        // ... but this one needs to be activated for releases
        $this->registerHook('entry_before_insert', array($this, 'embedTeemVideo'));

        $this->loadConfigValues();
    }

    /**
     * Inserts the YouTube video from the linked Teem page into the content of an entry.
     *
     * @param FreshRSS_Entry $entry
     * @return mixed
     */
    public function embedTeemVideo($entry)
    {
        /** @var YouTubeExtension $youtube */
        $youtube = Minz_ExtensionManager::findExtension('YouTube Video Feed');
        if ($youtube === null) {
            return;
        }

        $link = $entry->link();

        if (stripos($link, '://jointheteem.com/') === false) {
            return $entry;
        }

        $this->loadConfigValues();

        $html = '';

        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTMLFile($link);
        libxml_use_internal_errors(false);
        $xpath = new DOMXpath($dom);

        $container = $xpath->query("//a[contains(@class, 'lazy-load-youtube')]");
        if (!is_null($container)) {
            $tag = $container->item(0);
            if ($tag !== null) {
                $youtubeUrl = $tag->nodeValue;
                $html = $youtube->getIFrameForLink($youtubeUrl);
            }
        }

        // TODO support vimeo videos
        if (false && empty($html)) {
            $container = $xpath->query("//div[contains(@class, 'lazy-load-vimeo')]");
            if (!is_null($container)) {
                // TODO find javascript child node and parse the src attribute for the video id, eg <script src="//vimeo.com/api/v2/video/236412836.json.json?callback=showThumb">
                // then create the iframe <iframe src="//player.vimeo.com/video/236412836?autoplay=1&amp;color=00adef" style="height: 473px; width: 840px;" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" autoplay="" allowfullscreen="" height="473" width="840"></iframe>
                $url = '';
                $html = $youtube->getIFrameHtml($url);
            }
        }

        if (empty($html) || $this->showContent) {
			$html .= $entry->content();
		}

        $originalHash = $entry->hash();
        $entry->_content($html);
        $entry->_hash($originalHash);

        return $entry;
    }

    /**
     * Initializes the extension configuration, if the user context is available.
     */
    protected function loadConfigValues()
    {
        if (!class_exists('FreshRSS_Context', false) || null === FreshRSS_Context::$user_conf) {
            return;
        }

        if (FreshRSS_Context::$user_conf->teem_show_content != '') {
            $this->showContent = (bool)FreshRSS_Context::$user_conf->teem_show_content;
        }
    }

    /**
     * Saves the user settings for this extension.
     */
    public function handleConfigureAction()
    {
        $this->registerTranslates();
        $this->loadConfigValues();

        if (Minz_Request::isPost()) {
            FreshRSS_Context::$user_conf->teem_show_content = (int)Minz_Request::param('teem_show_content', 0);
            FreshRSS_Context::$user_conf->save();
        }
    }
}

