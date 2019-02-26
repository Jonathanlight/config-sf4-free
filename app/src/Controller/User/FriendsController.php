<?php

namespace App\Controller\User;

use App\Manager\CryptoManager;
use App\Repository\FriendsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FriendsController extends AbstractController
{
    /**
     * @Route("/user/friends/{_locale}", name="friends", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function friends(FriendsRepository $friendsRepository)
    {
        return $this->render('user/friends.html.twig', [
            'allFriends' => $friendsRepository->getFriendByUser($this->getUser()),
            'allAskFriends' => $friendsRepository->getFriendAskByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/user/active/friend/{_locale}", name="activeFriend", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return RedirectResponse
     */
    public function activeFriend(Request $request, CryptoManager $cryptoManager)
    {
        if ($request->isMethod('POST')) {
            $cryptoManager->activeFriend($request->request->all());
        }

        return $this->redirectToRoute('mes-amis');
    }

    /**
     * @Route("/user/add/friend/{_locale}", name="addFriend", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return RedirectResponse
     */
    public function addfriends(Request $request, CryptoManager $cryptoManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $cryptoManager->addFriend($user, trim($data['emailFriend']));
        }

        return $this->redirectToRoute('mes-amis');
    }
}
