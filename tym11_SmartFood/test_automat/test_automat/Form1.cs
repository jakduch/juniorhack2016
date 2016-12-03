using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.IO;
using System.IO.Ports;
using System.Threading;
using MySql.Data.MySqlClient;
using MySql.Data;

namespace test_automat
{
    public partial class Form1 : Form
    {
        private Thread readThread;
        public Form1()
        {
            InitializeComponent();
        }

        private string Port;

        private void button1_Click(object sender, EventArgs e)
        {
            Port = null;
            string[] ports = SerialPort.GetPortNames();
            foreach (string port in ports)
            {
                try
                {
                    using (System.IO.Ports.SerialPort sp = new System.IO.Ports.SerialPort(port, 9600, System.IO.Ports.Parity.None, 8, System.IO.Ports.StopBits.One))
                    {
                        readThread = new Thread(Read);
                        sp.ReadTimeout = 2000;
                        sp.Open();
                        readThread.Start();
                        sp.ReadLine();
                        Port = port;
                        break;
                    }
                }
                catch
                { }
            }
            if (Port != null)
            {
                using (System.IO.Ports.SerialPort sp = new System.IO.Ports.SerialPort(Port, 9600, System.IO.Ports.Parity.None, 8, System.IO.Ports.StopBits.One))
                {
                    sp.Open();
                    sp.Encoding = Encoding.ASCII;
                    sp.WriteLine("test");

                    MessageBox.Show("test");
                    sp.Close();
                }



            }
            else MessageBox.Show("Není připojen automat!");


            }

        /*using (var sp = new System.IO.Ports.SerialPort("COM8", 115200, System.IO.Ports.Parity.None, 8, System.IO.Ports.StopBits.One))
        {
            sp.Open();

            sp.WriteLine("Hello!");

            var readData = sp.ReadLine();
            MessageBox.Show(readData);
        }*/
        private void Read()
        {
            Thread.Sleep(2000);
        }

        private void button2_Click(object sender, EventArgs e)
        {
            byte x = 55;
            int id;


            MySqlConnection pripojeni = new MySqlConnection("Database=hackathon;DataSource=192.168.133.193;UserId=david;Password=123");
            MySqlCommand mc = new MySqlCommand();
            mc.CommandText = "";




            //myConnectionString = "server=192.168.133.193;uid=david;" +
            //    "pwd=123;database=hackathon;";
        }
    }
        

    
}