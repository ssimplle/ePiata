<?php

class AuthService{

    public function __construct(private PDO $pdo)
    {}

    public function register(string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1'
        );
    
        $stmt->execute([$email]);
        if ($stmt->fetch()) return false;

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $addUserStmt = $this->pdo->prepare(
            'INSERT INTO users (email, password)
            VALUES (?, ?)'
        );

        if ($addUserStmt->execute([$email, $passwordHash])) {
            $emailBody = buildEmailTemplate(
                'Bine ai venit pe ePiata!',
                'Contul tau a fost creat cu succes. Acum poti explora produsele locale, salva favorite si plasa comenzi rapid.',
                'Intra in platforma',
                'http://localhost/ePiata/public/index.php?page=login'
            );

            sendEmail($email, 'Bine ai venit pe ePiata!', $emailBody);
            return true;
        } else {
            return false;
        }

    }

    public function login(string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, password
            FROM users
            WHERE email = ?
            LIMIT 1'
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user) return false;

        if (!password_verify($password, $user['password'])) return false;

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];

        $emailBody = buildEmailTemplate(
            'Autentificare reusita',
            'Te-ai autentificat cu succes in contul tau ePiata. Iti dorim cumparaturi placute si comenzi usoare.',
            'Deschide Dashboard',
            'http://localhost/ePiata/public/index.php?page=dashboard'
        );

        sendEmail($email, 'Autentificare reusita', $emailBody);
        return true;

    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
    }

   
}