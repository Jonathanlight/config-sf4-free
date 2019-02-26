<?php

namespace App\DataFixtures;

use App\Entity\Crypto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CryptoTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cryptos = [
            ['name' => 'Bitcoin', 'indice' => 'XBT', 'image' => 'btc.png'],
            ['name' => 'Bitcoin Cash', 'indice' => 'BCH', 'image' => 'bch.png'],
            ['name' => 'Ethereum', 'indice' => 'ETH', 'image' => 'ethereum.png'],
            ['name' => 'Ethereum Classic', 'indice' => 'ETC', 'image' => 'etc.png'],
            ['name' => 'Litecoin', 'indice' => 'LTC', 'image' => 'ltc.png'],
            ['name' => 'Zcash', 'indice' => 'ZEC', 'image' => 'zec.png'],
            ['name' => 'Ripple', 'indice' => 'XRP', 'image' => 'ripple.png'],
            ['name' => 'EOS', 'indice' => 'EOS', 'image' => 'eos.png']
        ];

        foreach ($cryptos as $key => $crypto) {
            $cryptoSet = "emManager".$crypto['indice'];
            $$cryptoSet = new Crypto();
            $$cryptoSet->setName($crypto['name']);
            $$cryptoSet->setIndice($crypto['indice']);
            $$cryptoSet->setImage($crypto['image']);
            $$cryptoSet->setCreated(new \DateTime());
            $manager->persist($$cryptoSet);
        }

        $manager->flush();
    }
}
