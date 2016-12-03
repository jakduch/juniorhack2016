using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace test_serial
{
    public partial class Form1 : Form
    {
        private bool[] b = new bool[] { false };
        private Color t = Color.White,
                      f = Color.Black;
        private int l = 0;

        public Form1()
        {
            InitializeComponent();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            Send(44);

        }
        
        public bool[] Send(byte x)
        {
            bool[] b = ToBools(x);
            List<bool> o = new List<bool>();
            
                for (byte i = 0; i < 4; i++)
                {
                    if (i % 2 == 0) o.Add(true); else o.Add(false);
                }

                for (byte i = 0; i < 8; i++)
                {
                    if (b[i])
                    {
                        o.Add(true);
                        o.Add(true);
                    }
                    else
                    {
                        o.Add(false);
                        o.Add(false);
                    }
                }
            return o.ToArray();
        }

        private bool[] ToBools(byte input)
        {
            List<bool> o = new List<bool>();

            for (double d = 128; d != 0.5; d /=2)
            {
                if (input >= d)
                {
                    o.Insert(0, true);
                    input -= (byte)d;
                }
                else
                {
                    o.Insert(0, false);
                }
            }



            return o.ToArray();
        }
        
        private void timer1_Tick_1(object sender, EventArgs e)
        {
            if (b[l])
            {
                BackColor = t;
            }
            else
            {
                BackColor = f;
            }
            if (l == b.Length-1)
            {
                l = 0;
            }
            else
                l++;


        }

        private void button1_Click(object sender, EventArgs e)
        {
            byte x = Convert.ToByte(textBox1.Text);
            b = Send(x);



        }
    }
}
