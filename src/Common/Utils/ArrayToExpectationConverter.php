<?php

namespace Mcustiel\Phiremock\Common\Utils;

use Mcustiel\Phiremock\Domain\MockConfig;
use Mcustiel\Phiremock\Domain\Options\Priority;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Response;

class ArrayToExpectationConverter
{
    /** @var ArrayToRequestConditionConverter */
    private $arrayToRequestConverter;
    /** @var ArrayToResponseConverterLocator */
    private $arrayToResponseConverterLocator;

    public function __construct(
        ArrayToRequestConditionConverter $arrayToRequestConditionsConverter,
        ArrayToResponseConverterLocator $arrayToResponseConverterLocator
    ) {
        $this->arrayToRequestConverter = $arrayToRequestConditionsConverter;
        $this->arrayToResponseConverterLocator = $arrayToResponseConverterLocator;
    }

    /**
     * @param array $expectationArray
     *
     * @return MockConfig
     */
    public function convert(array $expectationArray)
    {
        $request = $this->convertRequest($expectationArray);
        $response = $this->convertResponse($expectationArray);
        $scenarioName = $this->getScenarioName($expectationArray);
        $priority = $this->getPriority($expectationArray);

        return new MockConfig($request, $response, $scenarioName, $priority);
    }

    /**
     * @param array $expectationArray
     *
     * @return null|\Mcustiel\Phiremock\Domain\Options\Priority
     */
    private function getPriority(array $expectationArray)
    {
        $priority = null;
        if (!empty($expectationArray['priority'])) {
            $priority = new Priority((int) $expectationArray['priority']);
        }

        return $priority;
    }

    /**
     * @param array $expectationArray
     *
     * @return null|\Mcustiel\Phiremock\Domain\Options\ScenarioName
     */
    private function getScenarioName(array $expectationArray)
    {
        $scenarioName = null;
        if (!empty($expectationArray['scenarioName'])) {
            $scenarioName = new ScenarioName($expectationArray['scenarioName']);
        }

        return $scenarioName;
    }

    /**
     * @param array $expectationArray
     *
     * @return Response
     */
    private function convertResponse(array $expectationArray)
    {
        if (!isset($expectationArray['response'])) {
            throw new \InvalidArgumentException('Creating an expectation without response.');
        }

        return $this->arrayToResponseConverterLocator
            ->locate($expectationArray['response'])
            ->convert($expectationArray['response']);
    }

    private function convertRequest(array $expectationArray)
    {
        if (!isset($expectationArray['request'])) {
            throw new \InvalidArgumentException('Expectation request is not set');
        }

        return $this->arrayToRequestConverter->convert($expectationArray['request']);
    }
}
