<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\RecordFoulCommand;
use App\Application\Exception\ApplicationException;
use App\Application\Exception\ValidationException;
use App\Application\Factory\MatchEventFactoryInterface;
use App\Application\Validator\MatchEventValidatorInterface;
use App\Domain\MatchEvent\Foul;
use App\Domain\MatchEvent\Repository\MatchEventRepositoryInterface;
use App\Domain\Match\VO\MatchId;
use App\Domain\Player\VO\PlayerId;
use App\Domain\Team\VO\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordFoulHandler
{
    public function __construct(
        private MatchEventRepositoryInterface $eventRepository,
        private MatchEventValidatorInterface $validator,
        private MatchEventFactoryInterface $factory,
    ) {
    }

    public function __invoke(RecordFoulCommand $command): Foul
    {
        try {
            $this->validator->validate($command->eventDTO->type, $command->eventDTO->data);

            $foul = $this->factory->createFoul(
                id: $command->matchEventId,
                matchId: new MatchId($command->eventDTO->data['match_id']),
                teamId: new TeamId($command->eventDTO->data['team_id']),
                committedBy: new PlayerId($command->eventDTO->data['committedBy']),
                sufferedBy: isset($command->eventDTO->data['sufferedBy'])
                    ? new PlayerId($command->eventDTO->data['sufferedBy'])
                    : null,
                minute: $command->eventDTO->data['minute'],
                second: $command->eventDTO->data['second']
            );

            $this->eventRepository->save($foul);

            return $foul;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ApplicationException(previous: $e);
        }
    }
}
