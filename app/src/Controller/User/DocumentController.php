<?php

namespace App\Controller\User;

use App\Manager\CryptoManager;
use App\Services\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DocumentController extends AbstractController
{
    /**
     * @Route("/user/upload/document/{_locale}", name="upload_document", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param UploadType $uploadType
     * @return RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function uploadDocument(Request $request, CryptoManager $cryptoManager, UploadType $uploadType)
    {
        $extention_ = null;
        $user = $this->getUser();

        if ($_FILES) {
            $pathname = "document/wxp/";

            if ($request->getHost() != 'localhost') {
                $path = 'https://cryptizy.com/'.$pathname;
            } else {
                $path = 'http://'.$request->getHttpHost().'/'.$pathname;
            }

            if (isset($_FILES['piece_didentite']['name'])) {
                $document = "JPI_".$user->getReference();
                if ($uploadType->upload($_FILES['piece_didentite'], $document, $pathname) === true) {
                    if (strstr($_FILES['piece_didentite']["type"], 'jpg')) {
                        $extention_ = strstr($_FILES['piece_didentite']["type"], 'jpg');
                    }
                    if (strstr($_FILES['piece_didentite']["type"], 'png')) {
                        $extention_ = strstr($_FILES['piece_didentite']["type"], 'png');
                    }
                    if (strstr($_FILES['piece_didentite']["type"], 'jpeg')) {
                        $extention_ = strstr($_FILES['piece_didentite']["type"], 'jpeg');
                    }
                    if (strstr($_FILES['piece_didentite']["type"], 'pdf')) {
                        $extention_ = strstr($_FILES['piece_didentite']["type"], 'pdf');
                    }

                    $uploadType->upload($_FILES['piece_didentite'], $document, $pathname);
                    $cryptoManager->loadIdentite($user, $document.'.'.$extention_, $path);
                }
            }

            if (isset($_FILES['verifyHome_image']['name'])) {
                $document = "JDD_".$user->getReference();
                if ($uploadType->upload($_FILES['verifyHome_image'], $document, $pathname) === true) {
                    if (strstr($_FILES['verifyHome_image']["type"], 'jpg')) {
                        $extention_ = strstr($_FILES['verifyHome_image']["type"], 'jpg');
                    }
                    if (strstr($_FILES['verifyHome_image']["type"], 'png')) {
                        $extention_ = strstr($_FILES['verifyHome_image']["type"], 'png');
                    }
                    if (strstr($_FILES['verifyHome_image']["type"], 'jpeg')) {
                        $extention_ = strstr($_FILES['verifyHome_image']["type"], 'jpeg');
                    }
                    if (strstr($_FILES['verifyHome_image']["type"], 'pdf')) {
                        $extention_ = strstr($_FILES['verifyHome_image']["type"], 'pdf');
                    }

                    $uploadType->upload($_FILES['verifyHome_image'], $document, $pathname);
                    $cryptoManager->loadJustificatifDomicile($user, $document.'.'.$extention_, $path);
                }
            }

            if (isset($_FILES['selfie_justify']['name'])) {
                $document = "JDS_".$user->getReference();
                if ($uploadType->upload($_FILES['selfie_justify'], $document, $pathname) === true) {
                    if (strstr($_FILES['selfie_justify']["type"], 'jpg')) {
                        $extention_ = strstr($_FILES['selfie_justify']["type"], 'jpg');
                    }
                    if (strstr($_FILES['selfie_justify']["type"], 'png')) {
                        $extention_ = strstr($_FILES['selfie_justify']["type"], 'png');
                    }
                    if (strstr($_FILES['selfie_justify']["type"], 'jpeg')) {
                        $extention_ = strstr($_FILES['selfie_justify']["type"], 'jpeg');
                    }
                    if (strstr($_FILES['selfie_justify']["type"], 'pdf')) {
                        $extention_ = strstr($_FILES['selfie_justify']["type"], 'pdf');
                    }

                    $uploadType->upload($_FILES['selfie_justify'], $document, $pathname);
                    $cryptoManager->loadJustificatifSelfie($user, $document.'.'.$extention_, $path);
                }
            }
        }

        return $this->redirectToRoute('verif');
    }
}
