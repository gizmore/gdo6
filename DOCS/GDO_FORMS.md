# GDO6 HTML Forms

In gdo6 forms are created by using the GDT_Form class.
It has the trait WithFields, which adds an array of GDT to it.
addField() can be used to add a GDT to the form.
There is MethodForm which is an abstract method to make use of GDT_Form.
The code looks like that:

    $form = GDT_Form::make();
    $form->addFields([
        GDT_Username::make('username')->unique()->notNull(),
        GDT_AntiCSRF::make(),
    ]);
    $form->actions()->addField(GDT_Submit::make());
    echo $form->render();
    
MethodForm also calls validation on the fields and checks if a submit button was pressed.
Here is an example on how to use MethodForm.

    final class MyForm extends MethodForm
    {
        public function createForm(GDT_Form $form)
        {
            $form->addFields([
                GDT_Username::make('username')->unique()->notNull(),
                GDT_Captcha::make(),
                GDT_AntiCSRF::make(),
            ]);
            $form->actions()->addField(GDT_Submit::make());
        }
    
        public function formValidated(GDT_Form $form) # default submit button pressed
        {
            $user = $form->getFormValue('username'); # GDO_User object
            return $this->message('msg_hello_user', [$user->displayNameLabel());
        }
    } 
    
This method is a complete example how to make simple forms.
