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
 * CQRS Bus DomainBus
 * 
 * This is our default bus. We've set it via configuration.
 * The DomainBus extends the CQRS AbstractBus and can thereby invoke comands,
 * publish events and execute queries. Only thing to do is, implement the getName method.
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class DomainBus extends AbstractBus
{
    const NAME = 'domain-bus';
    
    /**
     * Get the name of the bus
     * 
     * The name must be unique in the cqrs system.
     * 
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }    
}
