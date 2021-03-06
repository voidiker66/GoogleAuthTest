<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Google\Client as GoogleClient;

class DefaultController extends AbstractController
{

 	/**
     * @Route("/", name="app_dashboard")
     */
 	public function dashboard(Request $request)
 	{
 		return $this->render('base.html.twig');
 	}


    /**
     * @Route("/authHandler", name="auth_handler")
     */
    public function authHandler(Request $request)
    {
        // Here we handle the sucecssful signin request using the Google ID token
        $query = $request->request->all();
        $idToken = $query['idToken'];
        $client = new GoogleClient(['client_id' => $this->getParameter('app.google_client_id')]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            // ID token has been validated by Google API
            $userid = $payload['sub'];
            $userEmail = $payload['email'];

            /* 
                Here we would insert the logic for storing the user who completed the sign in.
                From this point forward, we have verified with Google that a user has completed a sign in
                on our server and we know the current signed in user.

                Data:
                    sub => Google Account ID
                    email => email account that was used to sign in
                    email_verified => boolean that indicates our server verified the account via Google API
                    name => full name associated with the account
                    given_name => first name associated with the account
                    family_name => last name associated with the account
            */

            /*
                For example, we could store the information in cache so we can retrieve the information anywhere on the local server
            */

            $return = null;

            // If the email was found, store the current user data to cache
            if (isset($userEmail)) {
                $this->saveItemToCache('app.current_user', ['user_id' => $userid, 'user_email' => $userEmail]);
            }


            // Get the new current user saved to cache
            $currentUser = $this->getItemFromCache('app.current_user');

            // Verify the current user exists
            if (! $currentUser->isHit()) {
                // Current user does not exist
                return new JsonResponse([
                    'status' => 'fail',
                    'data' => [
                        'err' => 'user does not exist in cache'
                    ]
                ]);
            }


            return new JsonResponse([
                'status' => 'ok',
                'current_user' => $currentUser->get(),
                'data' => $payload
            ]);
        } else {
            // Invalid ID token
            return new JsonResponse([
            'status' => 'fail',
            'data' => $payload // $payload should be false
          ]);
        }

    }

    private function getItemFromCache($item)
    {
        $cache = new FilesystemAdapter();
        $itemInCache = $cache->getItem($item);
        if ($itemInCache->isHit()) {
            return $itemInCache;
        }
        return null;
    }

    private function saveItemToCache($item, $value, $force=true)
    {
        $cache = new FilesystemAdapter();
        $itemInCache = $cache->getItem($item);
        if (!$itemInCache->isHit() || $force) {
            $itemInCache->set($value);
            $cache->save($itemInCache);
            return true;
        }
        return false;
    }


}