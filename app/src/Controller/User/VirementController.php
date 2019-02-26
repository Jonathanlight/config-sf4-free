<?php

namespace App\Controller\User;

use App\Form\User\VirementType;
use App\Manager\CryptoManager;
use App\Services\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class VirementController extends AbstractController
{
    /**
     * @Route("/user/virement/{_locale}", name="virement", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param MessageService $messageService
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function virement(
        Request $request,
        CryptoManager $cryptoManager,
        MessageService $messageService
    ) {
        $user = $this->getUser();

        $form = $this->createForm(VirementType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['montant_depot'] <= 100 and $data['montant_depot'] >= 20) {
                $montant = trim($data['montant']);

                if (!is_numeric($montant)) {
                    $messageService->addError('Montant non numérique');
                    return $this->redirectToRoute('virement');
                }

                $frais = round(0.019 * $montant + 0.25, 2);
                $solde = $montant - $frais;
                //$newUrl = $cryptoManager->virement($user, $solde, $montant, $frais);
                return $this->redirectToRoute('carte_bancaire', ['montant'=>100] );
            } else {
                $messageService->addError('Echec du depôt verifier que le montant soit compris entre 20€ et 1000€');
                return $this->redirectToRoute('virement');
            }
        }

        return $this->render('user/virement.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/cb/{_locale}", name="carte_bancaire", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function carteBancaire(Request $request): Response
    {
        return $this->render('user/depot.html.twig');
    }
}
