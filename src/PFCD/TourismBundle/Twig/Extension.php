<?php

namespace PFCD\TourismBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class Extension extends \Twig_Extension
{

	private $request;
	private $router;

	public function __construct(ContainerInterface $container)
    {
		// Just retrieve the router here
		$this->router = $container->get('router');
	}

	/*
	 * Listen to the 'kernel.request' event to get the main request, this has several reasons:
	 *  - The request can not be injected directly into a Twig extension, this causes a ScopeWideningInjectionException
	 *  - Retrieving the request inside of the 'localize_route' method might yield us an internal request
	 *  - Requesting the request from the container in the constructor breaks the CLI environment (e.g. cache warming)
	 */
	public function onKernelRequest(GetResponseEvent $event)
    {
		if ($event->getRequestType() === HttpKernel::MASTER_REQUEST) {
			$this->request = $event->getRequest();
		}
	}

	public function getFunctions()
	{
		return array(
			'localize_route' => new \Twig_Function_Method($this, 'localize_route')
		);
	}

	public function localize_route($locale = null)
    {
		// Merge query parameters and route attributes
		$attributes = array_merge($this->request->query->all(), $this->request->attributes->all());

		// Set locale
		$attributes['_locale'] = $locale ?: \Locale::getDefault();

		// Remove hidden route property
		unset($attributes['_route']);

		return $this->router->generate($this->request->attributes->get('_route'), $attributes);
	}

	public function getName()
	{
		return 'twig_localized_route_extension';
	}

}
