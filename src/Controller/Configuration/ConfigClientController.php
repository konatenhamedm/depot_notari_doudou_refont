<?php

namespace App\Controller\Configuration;

use App\Controller\BaseController;
use App\Repository\CiviliteRepository;
use App\Service\Breadcrumb;
use App\Service\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/admin/config/parametre/client')]
class ConfigClientController extends BaseController
{

    const INDEX_ROOT_NAME = 'app_config_parametre_client_index';


    /* private $menu;
     public function __construct(Menu $menu){
         $this->menu = $menu;
     }*/

    #[Route(path: '/', name: 'app_config_parametre_client_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {

        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        /* if($this->menu->getPermission()){
             $redirect = $this->generateUrl('app_default');
             return $this->redirect($redirect);
             //dd($this->menu->getPermission());
         }*/
        $modules = [
            [
                'label' => 'Particuliers',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_client_client_index', ['type' => 'P'])
            ],
            /*[
                'label' => 'Ressource humaine',
                'icon' => 'bi bi-truck',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'rh'])
            ],*/
            [
                'label' => 'Entreprises',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_client_client_index', ['type' => 'E'])
            ],


        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        return $this->render('config/parametre/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'permition' => $permission,
            'titre' => 'Configuration client',
            'type' => 'client'
        ]);
    }




    #[Route(path: '/{module}', name: 'app_config_parametre_client_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [

            'utilisateur' => [

                [
                    'label' => 'Groupes modules',
                    'id' => 'param_groupe_m',
                    'href' => $this->generateUrl('app_utilisateur_groupe_module_index')
                ],
                [
                    'label' => 'Module',
                    'id' => 'param_module',
                    'href' => $this->generateUrl('app_utilisateur_module_index')
                ],
                [
                    'label' => 'Permissions',
                    'id' => 'param_permission',
                    'href' => $this->generateUrl('app_utilisateur_permition_index')
                ],
                [
                    'label' => 'Les elements du menu',
                    'id' => 'param_permission_groupe',
                    'href' => $this->generateUrl('app_utilisateur_module_groupe_permition_index')
                ]

            ],


            'config' => [

                [
                    'label' => 'Civilité',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_parametre_civilite_index')
                ],
                [
                    'label' => 'Icons',
                    'id' => 'param_cm',
                    'href' => $this->generateUrl('app_parametre_icon_index')
                ],
                [
                    'label' => 'Configuration application',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_parametre_config_app_index')
                ]/* ,
                [
                    'label' => 'Entreprise',
                    'id' => 'param_entreprise',
                    'href' => $this->generateUrl('app_parametre_entreprise_index')
                ] */


            ],


        ];


        return $this->render('config/parametre/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
