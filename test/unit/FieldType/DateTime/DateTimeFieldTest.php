<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\DateTime;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\DateTime\DateTimeField
 * @covers ::<private>
 */
class DateTimeFieldTest extends TestCase
{
    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form()
    {
        $formBuilder = M::mock(FormBuilderInterface::class);
        $section = M::mock(SectionInterface::class);
        $sectionEntity = M::mock(FieldType::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);

        $dateTimeField = new DateTimeField();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['data' => '2015-12-25']]
                    ]
            ]
        );
        $dateTimeField->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(4);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with((string)$dateTimeField->getConfig()->getHandle(), DateTimeType::class, ['data'=>new \DateTime('2015-12-25')])
            ->andReturn($formBuilder);

        $dateTimeField->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(DateTimeField::class, $dateTimeField);
        $this->assertEquals($dateTimeField->getConfig(), $config);
    }
}
