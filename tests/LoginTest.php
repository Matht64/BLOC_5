<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\User;
use App\Models\User as UserModel;
use App\Utility\Hash;

class LoginTest extends TestCase
{
    private $fakeUser;

    protected function setUp(): void
    {
        $this->fakeUser = new User(route_params: []);
    }

    public function testLoginWithMissingFields()
    {
        $data = [
            'email' => 'test@example.com'
            // 'password' est manquant
        ];

        $result = $this->invokeMethod($this->fakeUser, 'login', [$data]);

        $this->assertFalse($result);
    }

    public function testLoginWithUserNotFound()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $userMock = $this->getMockBuilder(UserModel::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $userMock->method('getByLogin')
                 ->with($data['email'])
                 ->willReturn(null);

        $result = $this->invokeMethod($this->fakeUser, 'login', [$data]);

        $this->assertFalse($result);
    }

    public function testLoginWithIncorrectPassword()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $user = [
            'id' => 1,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'is_admin' => false,
            'password' => 'hashedpassword',
            'salt' => 'somesalt'
        ];

        $userMock = $this->getMockBuilder(UserModel::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $userMock->method('getByLogin')
                 ->with($data['email'])
                 ->willReturn($user);

        $result = $this->invokeMethod($this->fakeUser, 'login', [$data]);

        $this->assertFalse($result);
    }

    public function testSuccessfulLogin()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'correctpassword'
        ];

        $user = [
            'id' => 1,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'is_admin' => false,
            'password' => Hash::generate($data['password'], 'somesalt'),
            'salt' => 'somesalt'
        ];

        $userMock = $this->getMockBuilder(UserModel::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $userMock->method('getByLogin')
                 ->with($data['email'])
                 ->willReturn($user);

        $result = $this->invokeMethod($this->fakeUser, 'login', [$data]);

        $this->assertTrue($result);
    }

    /**
     * Appelle une méthode privée ou protégée d'une classe.
     *
     * @param object $object L'instance de la classe.
     * @param string $methodName Le nom de la méthode à appeler.
     * @param array $parameters Les paramètres à passer à la méthode.
     * @return mixed Le résultat de la méthode appelée.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
