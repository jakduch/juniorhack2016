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

        private int automat_id;
        private string Port;
        private System.IO.Ports.SerialPort SP;
        MySqlConnection pripojeni;

        private void button1_Click(object sender, EventArgs e)
        {
            try
            {
                automat_id = Convert.ToInt32(textBox1.Text);
                Port = null; if (SP != null) SP.Close(); SP = null;
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
                    SP = new System.IO.Ports.SerialPort(Port, 9600, System.IO.Ports.Parity.None, 8, System.IO.Ports.StopBits.One);
                    SP.Open();
                    SP.Encoding = Encoding.ASCII;
                    SP.WriteLine(("pripojeno       automat: " + automat_id).PadRight(32, ' '));
                    pripojeni = new MySqlConnection("Database=hackathon;DataSource=192.168.133.193;UserId=david;Password=123");
                    MessageBox.Show("pripojeno");
                    SP.WriteLine("Dobry den".PadRight(32, ' '));

                }
                else MessageBox.Show("Není připojen automat!");
            }
            catch
            {
                MessageBox.Show("problem s pripojenim");
            }
        }
        private void Read()
        {
            Thread.Sleep(2000);
        }

        private void button2_Click(object sender, EventArgs e)
        {
            try
            {
                byte x = 44;
                int cena = 0;



                pripojeni.Open();
                MySqlCommand mc = new MySqlCommand();
                mc.Connection = pripojeni;
                mc.CommandText = "SELECT * FROM orders WHERE order_number = " + x + " AND automat_id = " + automat_id + " AND getted = 0";
                MySqlDataReader cteni = mc.ExecuteReader();

                bool b = cteni.Read();

                if (b)
                {
                    do
                    {
                        cena = cteni.GetInt32("total_price");
                    }
                    while (cteni.Read());
                    SP.WriteLine((cena + "Kc").PadRight(32, ' '));
                }
                else
                    SP.WriteLine("Nemate nic      objednane.".PadRight(32, ' '));

            }
            catch
            {
                SP.WriteLine("chyba pripojeni");
                MessageBox.Show("chyba pripojeni");
            }
            finally
            {
                pripojeni.Close();
            }
                //myConnectionString = "server=192.168.133.193;uid=david;" +
            //    "pwd=123;database=hackathon;";
        }
    }
        

    
}