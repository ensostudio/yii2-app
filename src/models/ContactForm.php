<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * The model behind the contact form.
 */
class ContactForm extends Model
{
    /**
     * @var string
     */
    public string $subject;
    /**
     * @var string
     */
    public string $body;
    /**
     * @var string
     */
    public string $fromEmail;
    /**
     * @var string
     */
    public string $verifyCode;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['verifyCode', 'email', 'subject', 'body'], 'required'],
            ['verifyCode', 'captcha'],
            ['fromEmail', 'email'],
            [['subject', 'body'], 'string'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return [
            'subject' => 'Subject',
            'body' => 'Message',
            'fromEmail' => 'Your e-mail',
            'verifyCode' => 'Verification code',
        ];
    }

    /**
     * Sends the message to the specified e-mail address using the information collected by this form.
     *
     * @return bool whether the model passes validation
     */
    public function sendMail(): bool
    {
        if ($this->validate()) {
            return Yii::$app->mailer
                ->compose()
                ->setTo(Yii::$app->params['adminEmail'])
                ->setFrom($this->fromEmail)
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();
        }

        return false;
    }
}
