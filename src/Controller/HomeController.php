<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\FichierAdmin;
use App\Form\ClientType;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    use FileTrait;
    protected const UPLOAD_PATH = 'media_entreprise';
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/error_page', name: 'page_error_index', methods: ['GET', 'POST'])]
    public function errorIndex(Request $request): Response
    {
        return $this->render('error.html.twig', []);
    }

    private $em;
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }


    #[Route(path: '/print-iframe', name: 'default_print_iframe', methods: ["DELETE", "GET"], condition: "request.query.get('r')", options: ["expose" => true])]
    public function defaultPrintIframe(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $all = $request->query->all();
        //print-iframe?r=foo_bar_foo&params[']
        $routeName = $request->query->get('r');
        $title = $request->query->get('title');
        $params = $all['params'] ?? [];
        $stacked = $params['stacked'] ?? false;
        $redirect = isset($params['redirect']) ? $urlGenerator->generate($params['redirect'], $params) : '';
        $iframeUrl = $urlGenerator->generate($routeName, $params);

        $isFacture = isset($params['mode']) && $params['mode'] == 'facture' && $routeName == 'facturation_facture_print';

        return $this->render('home/iframe.html.twig', [
            'iframe_url' => $iframeUrl,
            'id' => $params['id'] ?? null,
            'stacked' => $stacked,
            'redirect' => $redirect,
            'title' => $title,
            'facture' => 0/*$isFacture*/
        ]);
    }
    #[Route('/get/fichier', name: 'fichier_index', methods: ['GET', 'POST'])]
    public function show(Request $request, FichierAdmin $fichier)
    {

        $fileName = $fichier->getFileName();
        $filePath = $fichier->getPath();
        $download = $request->query->get('download');

        $file = $this->getUploadDir($filePath . '/' . $fileName);

        if (!file_exists($file)) {
            return new Response('Fichier invalide');
        }

        if ($download) {
            return $this->file($file);
        }

        return new BinaryFileResponse($file);
    }

    #[Route('/new', name: 'app_client_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, Security $security,): Response
    {
        $client = new Client();
        $validationGroups = ['Default', 'FileRequired', 'oui'];
        $form = $this->createForm(ClientType::class, $client, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('app_client_client_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_parametre_client_index');


            if ($form->isValid()) {
                $client->setEntreprise($security->getUser()->getEmploye()->getEntreprise());
                $entityManager->persist($client);
                $entityManager->flush();

                $data = true;
                $message = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('client/client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
}
