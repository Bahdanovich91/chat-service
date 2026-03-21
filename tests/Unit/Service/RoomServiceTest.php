<?php

namespace Unit\Service;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Service\RoomService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class RoomServiceTest extends TestCase
{
    private $roomRepository;
    private $roomService;

    protected function setUp(): void
    {
        $this->roomRepository = $this->createMock(RoomRepository::class);
        $this->roomService = new RoomService($this->roomRepository);
    }

    public function testGetRoom(): void
    {
        $roomId = 1;
        $room = $this->createMock(Room::class);

        $this->roomRepository->expects($this->once())
            ->method('find')
            ->with($roomId)
            ->willReturn($room);

        $result = $this->roomService->getRoom($roomId);

        $this->assertSame($room, $result);
    }

    public function testGetRoomReturnsNullWhenNotFound(): void
    {
        $roomId = 999;

        $this->roomRepository->expects($this->once())
            ->method('find')
            ->with($roomId)
            ->willReturn(null);

        $result = $this->roomService->getRoom($roomId);

        $this->assertNull($result);
    }

    public function testGetAllRooms(): void
    {
        $room1 = $this->createMock(Room::class);
        $room2 = $this->createMock(Room::class);

        $messages1 = new ArrayCollection([1, 2, 3]);
        $messages2 = new ArrayCollection([1, 2]);

        $room1->method('getId')->willReturn(1);
        $room1->method('getName')->willReturn('Room 1');
        $room1->method('getMessages')->willReturn($messages1);

        $room2->method('getId')->willReturn(2);
        $room2->method('getName')->willReturn('Room 2');
        $room2->method('getMessages')->willReturn($messages2);

        $this->roomRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$room1, $room2]);

        $result = $this->roomService->getAllRooms();

        $expected = [
            ['id' => 1, 'name' => 'Room 1', 'messageCount' => 3],
            ['id' => 2, 'name' => 'Room 2', 'messageCount' => 2]
        ];

        $this->assertEquals($expected, $result);
    }
}
