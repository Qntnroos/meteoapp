<?php
// src/AppBundle/Controller/LuckyController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\DB;
use GuzzleHttp\Client;

class MeteoController extends Controller
{
    /**
     * @Route("/meteo/{delay}", name="meteo")
     */
    public function renderWeather(Request $request, $delay = 15)
    {
        $db = new DB();
        $values = $db-> getMeasurementByDelay($delay);
        $client = new Client(['base_uri' => 'http://localhost/cockpit/api/', 'timeout' => 2.0, ]);
        $req = $client->request(
            'POST',
            'collections/get/statics',
            ['headers' => ['Cockpit-Token' => '76c39541e834765969d28ba32c6bff'],
             'json' => ['filter' => ['name' => 'currentweather']]
            ]
        );
        $answer = json_decode($req->getBody());
        $local = gmdate('d M Y H:i', strtotime($values[0][1]));
        $val = ['meting' => $values, 'intro' => $answer->entries[0]->content, 'local' => $local];
        return $this->render("weather/renderWeather.html.twig", $val);
    }
}