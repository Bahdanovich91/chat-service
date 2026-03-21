<?php

namespace Unit\Service;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use App\Exceptions\RoomNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repository\MessageRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MessageServiceTest extends TestCase
{
    private MessageService $service;
    private $em;
    private $messageRepo;
    private $roomRepo;
    private $userRepo;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->messageRepo = $this->createMock(MessageRepository::class);
        $this->roomRepo = $this->createMock(RoomRepository::class);
        $this->userRepo = $this->createMock(UserRepository::class);

        $this->service = new MessageService(
            $this->em,
            $this->messageRepo,
            $this->roomRepo,
            $this->userRepo
        );
    }

    private function mockRoom(int $id): Room
    {
        $room = $this->createMock(Room::class);
        $room->method('getId')->willReturn($id);

        return $room;
    }

    private function mockUser(): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getName')->willReturn('User');

        return $user;
    }

    /**
     * @throws RoomNotFoundException
     * @throws UserNotFoundException
     */
    public function testSaveSuccessfully(): void
    {
        $room = $this->mockRoom(1);
        $user = $this->mockUser();

        $this->roomRepo->method('find')->willReturn($room);
        $this->userRepo->method('find')->willReturn($user);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->save(1, 1, 'Hello');

        $this->assertInstanceOf(Message::class, $result);
    }

    /**
     * @throws UserNotFoundException
     */
    public function testSaveThrowsRoomNotFoundException(): void
    {
        $this->roomRepo->method('find')->willReturn(null);
        $this->expectException(RoomNotFoundException::class);
        $this->service->save(999, 1, 'Hello');
    }

    /**
     * @throws RoomNotFoundException
     */
    public function testSaveThrowsUserNotFoundException(): void
    {
        $this->roomRepo->method('find')->willReturn($this->mockRoom(1));
        $this->userRepo->method('find')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        $this->service->save(1, 999, 'Hello');
    }

    public function testGetRecentMessages(): void
    {
        $roomId = 1;
        $messages = [];

        for ($i = 1; $i <= 2; $i++) {
            $message = $this->createMock(Message::class);
            $message->method('getId')->willReturn($i);
            $message->method('getContent')->willReturn("Message $i");
            $message->method('getSender')->willReturn($this->mockUser());
            $message->method('getRoom')->willReturn($this->mockRoom($roomId));
            $message->method('getCreatedAt')->willReturn(new \DateTimeImmutable());
            $messages[] = $message;
        }

        $this->messageRepo
            ->method('findByRoomAndDate')
            ->willReturn($messages);

        $result = $this->service->getRecentMessages($roomId, 30);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
    }
}
