<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 08/10/2018
 * Time: 00:12
 */

namespace App\Controller;

use App\Repository\CryptoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeoController extends AbstractController
{
    /**
     * @Route("/acheter-bitcoin/{_locale}", name="acheter-bitcoin", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterBitcoin(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterBitcoin.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-bitcoin-cash/{_locale}", name="acheter-bitcoin-cash", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterBitcoinCash(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterBitcoinCash.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-ethereum", name="acheter-ethereum", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterEthereum(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterEthereum.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-ethereum-classic", name="acheter-ethereum-classic", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterEthereumClassic(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterEthereumClassic.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-litecoin", name="acheter-litecoin", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterLitecoin(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterLitecoin.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-ripple", name="acheter-ripple", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterRipple(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterRipple.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-zcash", name="acheter-zcash", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterZcash(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterZcash.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/acheter-eos", name="acheter-eos", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function acheterEos(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/acheter/acheterEos.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-bitcoin", name="buy-bitcoin", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyBitcoin(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyBitcoin.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-bitcoin-cash", name="buy-bitcoin-cash", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyBitcoinCash(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyBitcoinCash.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-ethereum", name="buy-ethereum", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyEthereum(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyEthereum.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-ethereum-classic", name="buy-ethereum-classic", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyEthereumClassic(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyEthereumClassic.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-litecoin", name="buy-litecoin", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyLitecoin(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyLitecoin.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-zcash", name="buy-zcash", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyZcash(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyZcash.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-ripple", name="buy-ripple", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyRipple(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyRipple.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/buy-eos", name="buy-eos", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function buyEos(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('public/buy/buyEos.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/formation-blockchain", name="formation-blockchain", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function formationBlockcahin(): Response
    {
        return $this->render('public/formation-blockchain.html.twig');
    }
}