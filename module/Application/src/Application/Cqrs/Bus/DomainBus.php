<?php
/*
 * This file is part of the codeliner/zf2-cqrs-sample package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Cqrs\Bus;

use Cqrs\Bus\AbstractBus;
/**
 * Description of DomainBus
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class DomainBus extends AbstractBus
{
    const NAME = 'domain-bus';
    
    public function getName()
    {
        return self::NAME;
    }    
}
