<?php
/**
 * @package varnishCache
 */
/*
Plugin Name: Ergonet Cache
Description: Plugin per la gestione delle cache Nginx e Varnish su hosting Ergonet.
Version: 1.0.10
Author: Ergonet srl
Author URI: https://www.ergonet.it
License: GPLv2+
Text Domain: ergonet-varnish-cache
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

Copyright 2019-2022 Ergonet srl
 */

defined( 'ABSPATH' ) or die( 'Non puoi accedere a questa risorsa');

class varnishCache
{
    private $notToDo;

    function __construct()
    {
        $this->init();
    }

    function init()
    {
        add_action('save_post', array($this, 'purgeCache'));
        add_action('admin_bar_menu', array($this, 'adminBarMenu'), 99);
        add_action('admin_footer', array($this, 'cache_purge_action_js'));
        add_action('wp_ajax_varnish_cache_purge_homepage', array($this, 'cachePurgeHomepageCallback'));
        add_action('wp_ajax_varnish_cache_purge_all', array($this, 'cachePurgeAllCallback'));
        add_action('admin_menu', array($this, 'pluginInfo'));
    }

    function pluginInfo()
    {
        add_options_page("Varnish Cache", "Ergonet Varnish Cache", 'manage_options', 'varnishCacheInfo', array($this, 'pageContent'));
    }

    function pageContent()
    {
        echo "<h3>ERGONET VARNISH CACHE FOR WORDPRESS</h3><p>Ergonet Varnish Cache for WordPress è il plugin gratuito sviluppato da Ergonet srl, azienda di hosting italiana, per migliorare drasticamente le performance di un sito web sviluppato in WordPress.</p><h3>BENEFICI</h3><p>L’utilizzo di un sistema di reverse proxy basato sull’acceleratore web Varnish aumenterà le performance del tuo sito in WordPress da un minimo del 30% ad un massimo del 60%. Ricorda che la percentuale di miglioramento dipenderà comunque anche dall’intera strategia di ottimizzazione del tuo sito web.</p><h3>COS’È VARNISH?</h3><p>Varnish è un acceleratore http (detto anche reverse proxy) che salva intere pagine del tuo sito in WordPress nella memoria RAM, il supporto più veloce che ancora oggi esista, restituendole immediatamente al visitatore del sito web.</p><p>Se quindi una determinata pagina del tuo sito è già stata visitata in precedenza ed è quindi presente in cache, Varnish la restituirà immediatamente all’utente che la sta richiedendo, senza richiederne nuovamente la generazione al tuo sito in WordPress.</p><p>Qualora un visitatore visitasse una pagina non ancora presente in cache, Varnish la richiederà direttamente al web server Apache/NGINX, inserendola nella cache per velocizzare una possibile futura nuova richiesta della stessa.</p><h3>COMPATIBILITÁ</h3><p>Ergonet Varnish Cache è compatibile con tutti gli altri plugin di cache e ottimizzazione del sito web. Ad esempio JCH Optimize e JotCache.</p><p>Il plugin Ergonet Varnish Cache funziona esclusivamente come layer di comunicazione tra WordPress e il sistema di cache Varnish installato sulla piattaforma server di <b>hosting per WordPress con profilo Successo e cloud hosting dedicati (Solo e Multidominio)</b> di Ergonet disponibili per l’acquisto sul sito https://www.ergonet.it</p><p>Un requisito fondamentale per il funzionamento del plugin è l’attivazione del sistema di cache Varnish dal WebPanel (area clienti Ergonet disponibile su https://webpanel.ergonet.it) da parte dell’utente che ha acquistato il servizio di hosting condiviso o cloud dedicato.<p><h3>COME FUNZIONA IL PLUGIN</h3><p>Una volta attivato il sistema di cache Varnish dal WebPanel di Ergonet e installato e attivato il plugin Ergonet Varnish Cache, lo stesso funzionerà senza alcuna configurazione aggiuntiva.</p></b>Inserimento delle risorse in cache</b><p>Una risorsa, intesa come URL, verrà inserita nella cache di Varnish nel momento in cui un utente non loggato visiterà la pagina.</p><b>Aggiornamento delle risorse in cache</b><p>Ogni volta che il webmaster aggiornerà un articolo o una pagina, il plugin installato in WordPress si occuperà di eliminare la vecchia risorsa presente in cache, per evitare che gli utenti visualizzino un contenuto vecchio.</p><p>Contestualmente verrà anche eliminata la cache della homepage, per aggiornare gli eventuali ultimi articoli disponibili nella stessa.</p><b>Cancellazione delle risorse in cache</b><p>Considerata la funzionalità di aggiornamento e rimozione automatica delle risorse, non sarà necessario cancellare forzatamente la cache per alcun motivo. Inoltre la cancellazione totale della cache è un’azione sconsigliata, in quanto eliminerebbe dalla stessa tutte le risorse (pagine, articoli, ecc) che sono state immagazzinate in precedenza poiché molto visitate.</p><p>Su siti con moltissimi contenuti l’operazione di cancellazione totale della cache potrebbe causare inoltre un rallentamento importante, causato della necessità da parte del sito in WordPress di rigenerare nuovamente da zero tutte le pagine o articoli richieste dagli utenti. </p><h3>COSA NON VIENE INSERITO IN CACHE</h3><p>Per rendere efficiente il sistema di cache Varnish, ci sono alcune risorse e chiamate HTTP specifiche che non verranno mai inserite in cache, ecco quali:</p><ul style=\"list-style: disc;padding-inline-start: 40px;\"><li>Qualsiasi pagina, articolo/risorsa visitato da un utente loggato.</li><li>Qualsiasi pagina, articolo/risorsa in cui viene settato un header no-cache e similari.</li><li>Tutte le chiamate dirette al backend di WordPress.</li><li> Tutte le chiamate eseguite per inserimento dati (form di registrazione, contatti, commenti e così via.)</li></ul><h3>COME VERIFICARE CHE LA CACHE VARNISH SIA FUNZIONANTE</h3><p>Il sistema di cache Varnish setta degli header specifici nel caso la risorsa (link) richiamata sia presente o meno in cache. Per verificare se il sito web stia sfruttando la cache Varnish è quindi necessario verificare la presenza degli header specifici di Varnish.</p><p>Aprire gli strumenti per sviluppatori in base al browser utilizzato:</p><p>Firefox tramite il menù:</p><p> strumenti -> sviluppo web -> Attiva strumenti.</p><p>Chrome tramite il menù: Visualizza -> opzioni per sviluppatori -> strumenti per sviluppatori</p><p>A questo punto si apriranno gli strumenti per sviluppatori:</p><ul style=\"list-style: disc;padding-inline-start: 40px;\"><li>selezionare la scheda “Rete” oppure “Network”</li><li>selezionare la sotto-scheda “HTML” in Firefox oppure “Doc” in Chrome</li><li>ricaricare il sito web eseguendo un refresh della pagina</li><li>cliccare sulla riga relativa al nome dominio identificato con stato 200</li></ul><p>Nella colonna di destra si apriranno gli Header di risposta, l’ultimo header sarà quello relativo a Varnish:</p><p><b>X-VC-Cache: HIT</b>= la risorsa, quindi la pagina richiamata, è presente in cache ed è stata restituita da Varnish. Significa che il sistema di cache sta funzionando correttamente.</p><p><b>X-VC-Cache: MISS</b>= la risorsa, quindi la pagina richiamata, NON è presente in cache ed è stata generata dall’applicazione WordPress. Significa che il sistema di cache è stato istruito dagli header dell’applicazione oppure da regole personalizzate, a non restituire il contenuto presente in cache al visitatore.</p><h3>ATTIVAZIONE DEL SISTEMA DI CACHE VARNISH DAL WEBPANEL</h3><p>Dal WebPanel Ergonet sarà possibile impostare alcune configurazioni aggiuntive per Varnish:</p><b>Validità della cache</b><br>Parametro consigliato: <i>1 settimana</i><p>È il tempo di conservazione di una determinata risorsa in cache. Dopo questa tempistica impostata dall’utente, le risorse presenti nella cache di Varnish che avranno raggiunto tale periodo di conservazione, verranno eliminate.</p><p>Ricorda che una risorsa (ad esempio una pagina o un articolo) se modificata o aggiornata, verrà eliminata dalla cache di default, senza considerare il suo tempo di validità e senza necessità di cancellare tutta la cache. Ogni qualvolta aggiornerai una pagina o un articolo, anche la cache della homepage verrà invalidata.</p><b>Forza cache</b><br>Parametro consigliato: <i>OFF</i><p>Alcuni plugin o temi forzano gli header Cache Control in un formato che non permette a Varnish di inserire una risorsa in cache. È sempre preferibile non forzare questa direttiva, in quanto si prevede che gli header vengano correttamente settati dagli sviluppatori delle applicazioni, template ed estensioni.<br>Se attivata, è necessario verificare con accuratezza che non si presentino anomalie sul sito web.</p><b>Memoria dedicata (opzione disponibile solo su Cloud Hosting dedicati)</b><br>Parametro consigliato: <i>128MB</i><p>È la quantità di memoria RAM dedicata alla cache Varnish e 128MB è una quantità sufficiente per la maggior parte dei siti web. Per portali o ecommerce con molti contenuti o prodotti, è possibile impostare la cache a 256MB oppure a 512MB.<br>Tieni presente che più memoria RAM dedicherai a Varnish, meno ne rimarrà a disposizione per tutti gli altri siti web ospitati sullo stesso server cloud e per il sistema operativo. Aumenta questo parametro solo se hai sufficiente memoria RAM a disposizione.</p><b>Termini da escludere</b><p>Sono i termini e vocaboli eventualmente presenti nelle URL che, se presenti, fanno sì che Varnish non inserisca in cache quella specifica risorsa. Sono presenti già alcuni termini di default, ma l’elenco può essere arricchito all’occorrenza dall’utente.</p>";
    }

    function cache_purge_action_js() { ?>
        <script type="text/javascript" >
            jQuery("li#wp-admin-bar-cache-purge-homepage .ab-item").on( "click", function() {
                var data = {
                    'action': 'varnish_cache_purge_homepage',
                };

                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#wpbody-content').prepend('<div class="notice notice-success is-dismissible"><p>Cancellazione della homepage dalla cache Varnish avvenuta correttamente</p></div><br/>');
                });
            });

            jQuery("li#wp-admin-bar-cache-purge-all .ab-item").on( "click", function() {
                var data = {
                    'action': 'varnish_cache_purge_all',
                };

                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#wpbody-content').prepend('<div class="notice notice-success is-dismissible"><p>Cancellazione dell\'intera cache Varnish avvenuta correttamente</p></div><br/>');
                });
            });
        </script> <?php
    }

    function cachePurgeHomepageCallback() {
        $this->purgeFrontPage();
        wp_die();
    }

    function cachePurgeAllCallback() {
        $this->purgeAll();
        wp_die();
    }

    function adminBarMenu($wp_admin_bar) {
        global $pagenow;
        $wp_admin_bar->add_node(array("id"=>"parent_node_1", "title"=>"<span class=\"ab-icon dashicons dashicons-performance\"></span>Ergonet Varnish Cache", "href"=>false));
        $wp_admin_bar->add_group(array("id"=>"group_1", "parent"=>"parent_node_1"));
        $wp_admin_bar->add_group(array("id"=>"group_2", "parent"=>"parent_node_1"));

        $wp_admin_bar->add_node(array("id"=>"cache-purge-homepage", "title"=>"Aggiorna cache homepage", "href"=>"#", "parent"=>"group_1"));
        $wp_admin_bar->add_node(array("id"=>"cache-purge-all", "title"=>"Svuota tutta la cache Varnish", "href"=>"#", "parent"=>"group_1"));
        $wp_admin_bar->add_node(array("id"=>"child_node_4", "title"=>"Informazioni sul plugin", "href"=>admin_url('admin.php?page=varnishCacheInfo'), "parent"=>"group_2"));
    }

    function purgeCache($post_id)
    {
        if(!$this->notToDo) {
            $this->purgePost($post_id);
            $this->purgeFrontPage();
            $this->purgePostCategories($post_id);
            $this->notToDo = true;
        }
    }

    function purgeFrontPage()
    {
        $url = get_home_url();
        $command = $this->execCachePurge($url);
    }

    function populateFrontPageCache()
    {
        $url = get_home_url();
        $command = $this->populateCache($url);
    }

    function purgeAll()
    {
        $url = get_home_url()."/.*";
        $command = $this->execCachePurge($url);
        $url = get_home_url()."/*";
        $command = $this->execCachePurge($url);
    }

    function purgePost($post_id)
    {
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        $url = get_permalink( $post_id );
        $command = $this->execCachePurge($url);
    }

    function populatePostCache($post_id)
    {
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        $url = get_permalink( $post_id );
        $command = $this->populateCache($url);
    }

    function populatePostCategoriesCache($post_id)
    {
        $categorie = wp_get_post_categories($post_id);
        foreach ($categorie as $category_id) {
            $url = get_category_link($category_id);
            $command = $this->populateCache($url);
        }
    }

    function purgePostCategories($post_id)
    {
        $categorie = wp_get_post_categories($post_id);
        foreach ($categorie as $category_id) {
            $url = get_category_link($category_id);
            $command = $this->execCachePurge($url);
        }
    }

    function populateCache( $url )
    {
        $url = $this->getUrl($url);
        $params = array(
            'method' => 'GET',
            'user-agent' => 'wp_purgeCache',
            'sslverify' => false,
            'headers' => array(
                'host' => $url['hostname']
            ),
            'redirection' => 0,
        );
        $response = wp_remote_request($url['url'], $params);
    }

    function execCachePurge( $url )
    {
        $url = $this->getUrl($url);
        $params = array(
            'method' => 'PURGE',
            'user-agent' => 'wp_purgeCache',
            'sslverify' => false,
            'headers' => array(
                    'host' => $url['hostname']
            ),
            'redirection' => 0,
        );
        $response = wp_remote_request($url['url'], $params);
    }

    private function getUrl($url)
    {
        $parsedUrl = parse_url($url);
        $hostname = $parsedUrl['host'];
        $address = gethostbyname($hostname);
        $url = $parsedUrl['scheme'] . '://' . $address;
        if(array_key_exists('port', $parsedUrl)) {
            $url .= ':' . $parsedUrl['port'];
        }
        if(array_key_exists('path', $parsedUrl)) {
            $url .= $parsedUrl['path'];
        }
        if(array_key_exists('query', $parsedUrl)) {
            $url .= '?' . $parsedUrl['query'];
        }
        if(array_key_exists('fragment', $parsedUrl)) {
            $url .= '#' . $parsedUrl['fragment'];
        }
        return array(
            'url' => $url,
            'hostname' => $hostname
        );
    }
}

$varnishCache = new varnishCache();