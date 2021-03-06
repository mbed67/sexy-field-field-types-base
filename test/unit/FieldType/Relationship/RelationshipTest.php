<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Number;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\Relationship\Relationship;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Relationship\Relationship
 * @covers ::<private>
 */
class RelationshipTest extends TestCase
{
    /** @var FormBuilderInterface|M\Mock */
    private $formBuilder;

    /** @var SectionInterface|M\Mock */
    private $section;

    /** @var FieldType|M\Mock */
    private $sectionEntity;

    /** @var SectionManagerInterface|M\Mock */
    private $sectionManager;

    /** @var ReadSectionInterface|M\Mock */
    private $readSection;

    public function setUp()
    {
        $this->formBuilder = M::mock(FormBuilderInterface::class);
        $this->section = M::mock(SectionInterface::class);
        $this->sectionEntity = M::mock(FieldType::class);
        $this->sectionManager = M::mock(SectionManagerInterface::class);
        $this->readSection = M::mock(ReadSectionInterface::class);
    }

    /**
 * @test
 * @covers ::addToForm
 */
    public function it_adds_to_form_one_to_many()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'one-to-many',
                        'to' => 'pluto',
                        'form' => ['all' => ['relations']]
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $this->sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(9);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $sectionEntities = M::mock('alias:sexyEntities')->makePartial();

        $this->sectionEntity->shouldReceive('getPlutos')
            ->once()
            ->andReturn($sectionEntities);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $sectionEntities->shouldReceive('toArray')
            ->once()
            ->andReturn(['Uranus, Mars, Venus']);

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'plutos',
                ChoiceType::class,
                [
                    'choices' => ['planetarySexyEntry' => $mockEntry],
                    'data' => ['Uranus, Mars, Venus'],
                    'multiple' => true
                ]
            )
            ->andReturn($this->formBuilder);

        $relation->addToForm($this->formBuilder, $this->section, $this->sectionEntity, $this->sectionManager, $this->readSection);

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_many_to_one()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-one',
                        'to' => 'neptune',
                        'form' => ['all' => ['relations']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $this->sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(9);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getNeptune')
            ->once()
            ->andReturn($selectedEntity);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('planetarySexyEntry');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'neptune',
                ChoiceType::class,
                [
                    'choices' => ['...' => null, 'planetarySexyEntry' => $mockEntry],
                    'data' => $selectedEntity,
                    'multiple' => false
                ]
            )
            ->andReturn($this->formBuilder);

        $relation->addToForm($this->formBuilder, $this->section, $this->sectionEntity, $this->sectionManager, $this->readSection);

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_many_to_many()
    {
        $relation = new Relationship();
        $fieldConfig = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'kind' => 'many-to-many',
                        'to' => 'mistletoeRedpole',
                        'form' => ['all' => ['lots of relations']],
                        'variant' => 'not the variant you are looking for'
                    ]
            ]
        );
        $relation->setConfig($fieldConfig);

        $this->sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(9);

        $sectionTo = M::mock(SectionInterface::class)->makePartial();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andReturn($sectionTo);

        $sectionConfigTo = SectionConfig::fromArray(
            [
                'section' => [
                    'name' => 'nameOfSection',
                    'handle' => 'handleOfSection',
                    'fields' => ['1', '2', '3'],
                    'default' => 'sexyPerDefault',
                    'namespace' => 'the space has no name'
                ]
            ]
        );

        $sectionTo->shouldReceive('getConfig')
            ->once()
            ->andReturn($sectionConfigTo);

        $selectedEntity = M::mock('alias:selectedEntity')->makePartial();

        $this->sectionEntity->shouldReceive('getMistletoeRedpoles')
            ->once()
            ->andReturn($selectedEntity);

        $selectedEntity->shouldReceive('toArray')
            ->once()
            ->andReturn(['MarinatedHotham', 'GravyCreamBlizzard']);

        $mockEntry = M::mock('alias:entry')->makePartial();
        $mockEntry->shouldReceive('getDefault')
            ->once()
            ->andReturn('Red-Cloaked BrightBalls');

        $this->readSection->shouldReceive('read')
            ->once()
            ->andReturn(new \ArrayIterator([$mockEntry]));

        $this->formBuilder->shouldReceive('add')
            ->once()
            ->with(
                'mistletoeRedpoles',
                ChoiceType::class,
                [
                    'choices' => ['Red-Cloaked BrightBalls' => $mockEntry],
                    'data' => ['MarinatedHotham', 'GravyCreamBlizzard'],
                    'multiple' => true
                ]
            )
            ->andReturn($this->formBuilder);

        $relation->addToForm($this->formBuilder, $this->section, $this->sectionEntity, $this->sectionManager, $this->readSection);

        $this->assertInstanceOf(Relationship::class, $relation);
        $this->assertEquals($relation->getConfig(), $fieldConfig);
    }
}
