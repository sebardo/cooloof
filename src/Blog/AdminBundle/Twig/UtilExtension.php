<?php

namespace Blog\AdminBundle\Twig;

use Blog\CoreBundle\Entity\User;
use PEC\CoreBundle\Entity\Campo;
use PEC\CoreBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UtilExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    protected $em;

    protected $requestStack;
    
    protected $container;

    public function __construct($em, RequestStack $requestStack, $container)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function renderNumComments(User $user)
    {
        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $this->em->getRepository('CoreBundle:PostComment');

        $comments = $postCommentRespository->findByUserAndValidated($user);

        if (count($comments)) {
            return '<span class="badge bg-color-redLight pull-right inbox-badge">' . count($comments) . '</span>';
        }

        return '';
    }
    
    public function checkGoogleToken()
    {
        $client = new \Google_Client();
        $client->setClientId($this->container->getParameter('client_id'));
        $client->setClientSecret($this->container->getParameter('client_secret'));
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . '/admin/posts/new', FILTER_SANITIZE_URL);
        $client->setRedirectUri($redirect);

        // Define an object that will be used to make all API requests.
        $youtube = new \Google_Service_YouTube($client);
        if (isset($_GET['code'])) {
          if (strval($_SESSION['state']) !== strval($_GET['state'])) {
            die('The session state did not match.');
          }
          $client->authenticate($_GET['code']);
          $_SESSION['token'] = $client->getAccessToken();
          header('Location: ' . $redirect);
        }
        if (isset($_SESSION['token'])) {
          $client->setAccessToken($_SESSION['token']);
        }
        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) {
          $htmlBody = "<div class='alert alert-success'><h3>Autorización concedida</h3> <p>Ya puedes subir videos a YouTube<p>";
          $_SESSION['token'] = $client->getAccessToken();
        } else {
          // If the user hasn't authorized the app, initiate the OAuth flow
          $state = mt_rand();
          $client->setState($state);
          $_SESSION['state'] = $state;

          $authUrl = $client->createAuthUrl();
          $htmlBody = "<div class='alert alert-warning'><h3>Autorización requerida</h3> <p>Necesitas <a href=".$authUrl.">acceso</a> para poder subir videos a YouTube.<p></div>";
 
        }
        
        return $htmlBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'render_num_comments' => new \Twig_Function_Method($this, 'renderNumComments', array('is_safe' => array('html'))),
            'check_google_token' => new \Twig_Function_Method($this, 'checkGoogleToken', array('is_safe' => array('html'))),
            
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'util_extension';
    }
}
